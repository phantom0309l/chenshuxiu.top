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

class Patientpgroupref_join_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:20 加入课程的日期+1天，对所有患者进行课程规则的提醒';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        //只针对方寸儿童管理服务平台的患者
        $unitofwork = BeanFinder::get("UnitOfWork");
        $time = time() - 86400;
        $thedate = date("Y-m-d", $time);
        $sql = "select id from patientpgrouprefs where startdate = :thedate and status=1 and createtime > '2017-04-03'";

        $bind = [];
        $bind[":thedate"] = $thedate;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            echo "\n====id[{$id}]===" . XDateTime::now();
            $i ++;
            if ($i >= 5) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $patientpgroupref = PatientPgroupRef::getById($id);
            if( false == $patientpgroupref instanceof PatientPgroupRef ){
                continue;
            }

            $patient = $patientpgroupref->patient;
            if( false == $patient instanceof Patient ){
                continue;
            }

            if($patient->isInHezuo("Lilly")){
                continue;
            }

            if(1 == $patient->doubt_type){
                continue;
            }

            $sendContent = $this->getSendContent($patientpgroupref);
            $wxuser = $this->getWxUser($patientpgroupref);
            $this->sendmsg($wxuser, $sendContent);
        }

        $unitofwork->commitAndInit();
    }

    private function getWxUser ($patientpgroupref) {
        $wxuser = $patientpgroupref->wxuser;
        if (false == $wxuser instanceof WxUser) {
            $patient = $patientpgroupref->patient;
            $wxuser = $patient->getMasterWxUser();
        }
        return $wxuser;
    }

    private function getSendContent ($patientpgroupref) {
        $patient = $patientpgroupref->patient;
        $course = $patientpgroupref->pgroup->course;
        $patientname = $patient->name;
        $lesson_cnt = $course->getLessonCnt();
        $lessons = $course->getLessons();
        $day_cnt = 0;
        $open_duration_arr = array();
        foreach ($lessons as $a) {
            $open_duration = $a->open_duration;
            $open_duration_arr[] = $open_duration;
            $day_cnt += $open_duration;
        }

        $detail_str_arr = array();
        foreach($open_duration_arr as $i => $open_duration){
            $num = $this->numberToNumberOfChina($i+1);
            $str = "第{$num}节{$open_duration}天";
            $detail_str_arr[] = $str;
        }

        $detail_str = implode("，", $detail_str_arr) . "。";

        $arr = array(
            '#patient_name#' => $patientname,
            '#lesson_cnt#' => $lesson_cnt,
            '#detail_str#' => $detail_str,
        );
        $wxuser = $this->getWxUser($patientpgroupref);
        return MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'pgroup_join_notice', $arr);
    }

    private function numberToNumberOfChina($num){
        $arr = array(
            "零", "一", "二", "三", "四", "五", "六", "七", "八", "九", "十"
        );
        return isset( $arr[$num] ) ? $arr[$num] : $num;
    }

    private function sendmsg ($wxuser, $sendContent) {
        // 得到模板内容
        if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
            $patient = $wxuser->user->patient;
            if(false == $patient instanceof Patient){
                return;
            }

            if($patient->isInHezuo("Lilly")){
                $str = "向日葵关爱行动";
            }else {
                $str = "医生助理";
            }
            $first = array(
                "value" => "",
                "color" => "");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $sendContent,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);
        }
    }

}

// //////////////////////////////////////////////////////

$process = new Patientpgroupref_join_notice(__FILE__);
$process->dowork();
