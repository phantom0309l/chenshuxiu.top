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

class Lilly_patient_survey extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 09:00 发送合作患者满意度调查问卷';
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

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id
            from patient_hezuos
            where status=1 and company='Lilly'
            and (datediff(now(), createtime)=56 or datediff(now(), createtime)=140) ";

        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "\n====id[{$id}]===" . XDateTime::now();

            $patient_hezuo = Patient_hezuo::getById($id);
            $diff = $patient_hezuo->getDayCntFromCreate();

            $patient = $patient_hezuo->patient;
            if(false == $patient instanceof Patient){
                continue;
            }

            //患者报到第56天和140天发送满意度调查问卷；
            if($diff == 56){
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170533";
                $this->sendmsg($patient, $url);
            }

            if($diff == 140){
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170534";
                $this->sendmsg($patient, $url);
            }

        }

        $unitofwork->commitAndInit();
    }

    private function sendmsg ($patient, $url) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "关爱中心服务调查";
            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_patient_survey');
            $first = array(
                "value" => "",
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_survey(__FILE__);
$process->dowork();
