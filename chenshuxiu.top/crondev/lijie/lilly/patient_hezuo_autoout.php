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

$patientid = $argv[1];

class Patient_hezuo_autoout
{
    public function dopush ($patientid) {

        $today = date('Y-m-d');

        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
        if(false == $patient_hezuo instanceof Patient_hezuo){
            echo "\n=========没有找到合作患者！";
            return;
        }

        $patient = $patient_hezuo->patient;

        //患者入组超过7*25天时，记录结束时间，推送项目结束通知；
        $patient_hezuo->goOut(2);
        $this->sendmsg($patient);

        $unitofwork->commitAndInit();
        echo "\n=========发送成功！";
    }

    private function sendmsg ($patient) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "向日葵关爱行动";
            $content = "sunflower 管理服务项目已结束，您将继续方寸儿童管理服务平台管理服务，如有任何疑问，请发送消息与关爱专员联系。";
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

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);
        }
    }

}

// //////////////////////////////////////////////////////

$process = new Patient_hezuo_autoout(__FILE__);
$cnt = $process->dopush($patientid);
