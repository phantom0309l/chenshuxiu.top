<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Output_5939
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $allWxTxtMsgs = $this->getAllWxTxtMsgs();   // 获取所有 患者发送的消息



        $i = 0;
        $data = array();
        foreach ($allWxTxtMsgs as $key=>$wxTxtMsg) {
            // 不是ADHD 的患者，直接跳过
            if(!$this->isADHD($wxTxtMsg['wxuserid'])){
                continue;
            }
            $temp = [];
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $startWtmTime = $wxTxtMsg['createtime'];
            $overTime = date('Y-m-d H:i:s',time());

            // 是否报到
            if($wxTxtMsg['patientid'] == 0){
                $pushMsgs = $this->getPushMsgByWTxtMsg('wxuserid',$wxTxtMsg['wxuserid'],$startWtmTime,$overTime);
                $temp[] = '未报到';
                $temp[] = $wxTxtMsg['wxuserid'];
            }else{
                $pushMsgs = $this->getPushMsgByWTxtMsg('patientid',$wxTxtMsg['patientid'],$startWtmTime,$overTime);
                $temp[] = '已报到';
                $temp[] = $wxTxtMsg['patientid'];
            }

            $temp[] = $wxTxtMsg['createtime'];
            $temp[] = $wxTxtMsg['content'];

            if(!empty($pushMsgs)){
                $temp[] = $pushMsgs['content'];
                $temp[] = $pushMsgs['name'];
            }

            // 当患者发送的消息内容大于三字时，才会统计
            if($this->isstatistics($wxTxtMsg['content'])){
                  $data[] = $temp;
            }
        }

        $headarr = array(
            "是否报道",
            "患者ID",
            "消息日期",
            "消息内容",
            "此消息之后最近的运营回复内容",
            "运行姓名",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/yangshujie/scale/output_5939_" .time().
            ".xlsx");
        $unitofwork->commitAndInit();  foreach($data as $key=>$item){
                    if($item[1] == $temp[1]){
                        array_splice($data,$key,0,$temp);
                    }
                }
    }


    /***patientid
     * 获取所有患者发送的信息
     * @return mixed
     */
    private function getAllWxTxtMsgs () {
        $sql = "select wtm.id,p.patientid,wtm.createtime,wtm.content,p.wxuserid
                      from pipes as p left join wxtxtmsgs as wtm on p.objid = wtm.id
                      where
                           p.objtype='WxTxtMsg'
                      and
                         p.createtime > '2018-03-01 00:00:00'
                      order by p.wxuserid asc
                         ";

        $wxTxtMsgs = Dao::queryRows($sql);

        return $wxTxtMsgs;
    }


    /***
     * 获取对指定患者的 指定时间间隔 间的 运营发送的消息
     * @param $dimension  统计的维度，报道患者根据 patientid 统计，未报道患者根据 wxuserid 统计
     * @param $id
     * @param $createtime
     * @param $nextOneTime
     * @return mixed
     */
    private function getPushMsgByWTxtMsg ($dimension,$id,$createtime,$nextOneTime) {
        $bind = [];
        $sql = "select pm.patientid,pm.objtype,pm.objid,pm.sendway,pm.content as content,pm.send_response_code,pm.send_response_str,pm.send_by_objid,a.name as name
                      from pipes as p left join pushmsgs as pm on p.objid = pm.id left join auditors as a on pm.send_by_objid = a.id
                      where p.objtype='PushMsg'
                      AND
                          pm.{$dimension}=:id
                      AND
                          p.createtime > :createtime
                      AND
                          p.createtime < :nexttime
                      AND
                          pm.send_by_objtype='Auditor'
                      AND
                          pm.send_by_objid>1
                      limit 1
                          ";
        $bind[':id']=$id;
        $bind[':createtime']=$createtime;
        $bind[':nexttime']=$nextOneTime;
//        echo $id.'------'.$createtime.'------'.$nextOneTime;

        $pushmsgs = Dao::queryRow($sql,$bind);

        return $pushmsgs;
    }

    /***
     * 查询患者发送的下一条消息的时间
     * @param $wtmCreatetime
     * @param $wtmPatientid
     * @return false|int
     */
    private function getNextWtmTime ($wtmCreatetime,$wtmPatientid) {
        $sql = "select createtime from wxtxtmsgs where patientid=:patientid and createtime > :createtime limit 1";
        $bind[':patientid'] = $wtmPatientid;
        $bind[':createtime'] = $wtmCreatetime;

        $createtime = Dao::queryValue($sql,$bind);
        $createtime = empty($createtime)?date('Y-m-d H:i:s',time()):$createtime;
        return $createtime;
    }

    /***
     * 判断给定字符串的长度是否大于等于3
     * @param $str
     * @return bool
     */
    private function isstatistics ($str) {
        // 过滤标点符号
        $reg = "/[[:punct:]]/i";
        $str=urlencode($str);//将关键字编码
        $str=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/",'',$str);
        $str=urldecode($str);//将过滤后的关键字解码 echo $keyword;

        if(strlen($str)>6){
            return true;
        }else{
            return false;
        }
    }

    /***
     * 获取所有运营回复的信息
     * @return mixed
     */
    private function getAllPushMsgs () {
        $sql = "select pm.patientid,pm.objtype,pm.objid,pm.sendway,pm.content,pm.send_response_code,pm.send_response_str,pm.send_by_objid,a.name
                      from pipes as p left join pushmsgs as pm on p.objid = pm.id left join auditors as a on pm.send_by_objid = a.id
                      where p.objtype='PushMsg'
                      and
                          DATE_FORMAT(p.createtime,'%Y-%m-%d %h:%i:%s') > DATE_FORMAT('2018-03-01 00:00:00','%Y-%m-%d %h:%i:%s')
                      AND
                          pm.send_by_objtype='Auditor'
                      AND
                          pm.send_by_objid>1
                          ";

        $pushmsgs = Dao::queryRows($sql);

        return $pushmsgs;
    }


    /***
     * 根据患者id 对数据进行排序
     * @param array $data
     * @return array
     */
    private function statisticsByPatient (Array $data) {
        $result = [];
        $patientid = '';

        foreach($data as $key=>$item){
            $patientid = $item['patientid'];
            array_push($result,$item);
            print_r($item);
            unset($data[$key]);
            foreach($data as $k=>$i){
                if($i['patientid'] == $patientid){
                    array_push($result,$i);
                    print_r($i);
                    unset($data[$i]);
                }
            }
        }
        return $result;
    }


    private function isADHD ($wxuserid) {
        $wxuser = WxUser::getById($wxuserid);

        if($wxuser->wxshopid == 1){
            return true;
        }else {
            return false;
        }

    }


}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5836.php]=====");

$process = new Output_5939();
$process->dowork();

Debug::trace("=====[cron][end][Output_5836.php]=====");
Debug::flushXworklog();
echo "郭满果\n-----end----- " . XDateTime::now();
