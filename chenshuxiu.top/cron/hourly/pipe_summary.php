<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Pipe_summary extends CronBase
{

    protected static $desc_arr = array(
        "DrugItem_create" => "填写用药",
        "LessonUserRef_hwk" => "填写作业",
        "Paper_scale" => "填写评估量表",
        "Paper_wenzhen" => "填写问诊量表",
        "Patient_baodao" => "患者报到",
        "PatientPgroupRef_apply" => "新入组",
        "PushMsg_create" => "运营消息",
        "WxPicMsg_create" => "图片消息",
        "WxTxtMsg_create" => "患者文本消息",
        "WxVoiceMsg_create" => "语音消息",
        "WxUser_scan" => "扫码",
        "WxUser_subscribe" => "关注",
        "WxUser_unsubscribe" => "取消关注");

    protected static $adhd_arr = array(
        "DrugItem_create",
        "LessonUserRef_hwk",
        "Paper_scale",
        "Paper_wenzhen",
        "Patient_baodao",
        "PatientPgroupRef_apply",
        "PushMsg_create",
        "WxPicMsg_create",
        "WxTxtMsg_create",
        "WxVoiceMsg_create",
        "WxUser_scan",
        "WxUser_subscribe",
        "WxUser_unsubscribe");

    protected static $other_arr = array(
        "Patient_baodao",
        "PushMsg_create",
        "WxPicMsg_create",
        "WxTxtMsg_create",
        "WxVoiceMsg_create",
        "WxUser_scan",
        "WxUser_subscribe",
        "WxUser_unsubscribe");

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'hourly';
        $row["title"] = '每小时, 流汇总消息, 发送到关注的运营';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        $send_data = array();

        $time = time() - 3600;
        $time = date("Y-m-d H:i:s", $time);

        $sql = "select id from pipes where wxuserid>0 and createtime > :fromtime ";

        $bind = [];
        $bind[':fromtime'] = $time;

        $ids = Dao::queryValues($sql, $bind);

        // 汇总要发的数据存入$send_data
        foreach ($ids as $id) {
            $pipe = Pipe::getById($id);
            if ($pipe instanceof Pipe) {
                $wxuser = $pipe->wxuser;
                $wxshop = $wxuser->wxshop;

                // 20170419 TODO by sjp : 一个服务号多个疾病怎么办?
                $diseaseid = $wxshop->diseaseid;
                $objtype = $pipe->objtype;
                $objcode = $pipe->objcode;
                $str = $objtype . "_" . $objcode;

                // 满足类型的才进行计数
                if ($this->needCount($str, $diseaseid)) {

                    // 疾病数组是否生成
                    if (! isset($send_data[$diseaseid])) {
                        $send_data[$diseaseid] = array();
                    }

                    // 是否有对应类型
                    if (! isset($send_data[$diseaseid][$str])) {
                        $send_data[$diseaseid][$str] = 0;
                    }

                    $send_data[$diseaseid][$str] += 1;
                }
            }
        }

        // 给在职运营发送消息
        $this->sendmsg($send_data);
    }

    private function needCount ($str, $diseaseid) {
        if ($diseaseid == 1) {
            return in_array($str, self::$adhd_arr);
        } else {
            return in_array($str, self::$other_arr);
        }
    }

    private function sendmsg ($send_data) {

        if (count($send_data) == 0) {
            return;
        }

        $auditors = Dao::getEntityListByCond('Auditor', " and status = 1 and can_send_msg = 1 ");
        foreach ($auditors as $a) {
            if ($a->isHasRole(array(
                'product',
                'tech',
                'yunying'))) {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $auditorid = $a->id;
                echo "\n-----[auditor][{$auditorid}]----- \n";
                $this->sendmsgImp($a, $send_data);
                $unitofwork->commitAndInit();
            }
        }
    }

    private function sendmsgImp ($auditor, $send_data) {
        $userid = $auditor->userid;

        $auditordiseaserefs = AuditorDiseaseRefDao::getListByAuditor($auditor);

        foreach ($auditordiseaserefs as $a) {
            $disease = $a->disease;
            $wxshop = $disease->getWxShop();
            $wxuser = WxUserDao::getMasterWxUserByUserId($userid, $wxshop->id);
            if ($wxuser instanceof WxUser && 1 == $wxuser->subscribe) {
                $sendcontent = $this->getSendContent($wxuser, $send_data);
                if ($sendcontent) {
                    $wxuserid = $wxuser->id;
                    echo "\n-----[wxuserid][{$wxuserid}]----- \n";
                    PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $sendcontent);
                }
            }
        }
    }

    private function getSendContent ($wxuser, $send_data) {
        $str = "";
        $wxshop = $wxuser->wxshop;
        $diseaseid = $wxshop->diseaseid;
        $desc_arr = self::$desc_arr;

        $d = $send_data[$diseaseid];
        if (isset($d) && count($d) > 0) {
            $str = "\n按小时汇总信息：\n";
            foreach ($d as $key => $num) {
                $desc = $desc_arr[$key];
                $str .= $desc . " : " . $num . "\n";
            }
        }
        return $str;
    }

}

// //////////////////////////////////////////////////////

$process = new Pipe_summary(__FILE__);
$process->dowork();
