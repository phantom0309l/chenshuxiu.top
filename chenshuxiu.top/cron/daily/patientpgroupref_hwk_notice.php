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

class Patientpgroupref_hwk_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 20:20 在课文结束的前一天，对本课无作业记录的患者进行提醒';
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
        $time = time() + 2*86400;
        $thedate = date("Y-m-d", $time);
        $sql = "select id from studyplans where objcode='hwk' and done_cnt=0 and enddate=:thedate";

        $bind = [];
        $bind[":thedate"] = $thedate;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            echo "\n====id[{$id}]===\n" . XDateTime::now();
            $i ++;
            if ($i >= 5) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $studyplan = StudyPlan::getById($id);
            if( false == $studyplan instanceof StudyPlan ){
                continue;
            }

            //如果已出组跳过
            $patientpgroupref = $studyplan->patientpgroupref;
            $status = $patientpgroupref->status;
            if($status != 1){
                continue;
            }

            $patient = $studyplan->patient;
            if( false == $patient instanceof Patient ){
                continue;
            }

            if($patient->isInHezuo("Lilly")){
                continue;
            }

            if (false == $patient->isUnderControl()) {
                continue;
            }

            if(1 == $patient->doubt_type){
                continue;
            }

            $day = $studyplan->obj->open_duration;
            $seconds = strtotime($thedate) - time();
            $hour = floor($seconds/3600);
            $sendContent = $this->getSendContent($studyplan, $day, $hour);
            $wxuser = $this->getWxUser($studyplan);
            $this->sendmsg($wxuser, $sendContent);
        }

        $unitofwork->commitAndInit();
    }

    public function getWxUser ($studyplan) {
        $wxuser = $studyplan->wxuser;
        if (false == $wxuser instanceof WxUser) {
            $patient = $studyplan->patient;
            $wxuser = $patient->getMasterWxUser();
        }
        return $wxuser;
    }

    public function getSendContent ($studyplan, $day, $hour) {
        $patient = $studyplan->patient;
        $arr = array(
            '#patient_name#' => $patient->name,
            '#day#' => $day,
            '#hour#' => $hour,
        );
        $wxuser = $this->getWxUser($studyplan);
        return MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'pgroup_hwk_notice', $arr);
    }

    public function sendmsg ($wxuser, $sendContent) {
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

$process = new Patientpgroupref_hwk_notice(__FILE__);
$process->dowork();
