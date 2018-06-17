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
$patient_id = $argv[1];

//规定时间间隔催评估
class DrugScaleNoticeLilly_0
{
    public function dowork ($patient_id) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patient_id, " and status=1 ");
        if(false == $patient_hezuo instanceof Patient_hezuo){
            echo "\n=========没有找到合作患者！";
            return;
        }

        $patient = $patient_hezuo->patient;

        $this->sendmsg($patient);

        $unitofwork->commitAndInit();
        echo "\n=========成功！";
    }

    public function sendmsg ($patient) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "向日葵关爱行动";
            $content = $this->getSendContent($patient);
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

            $openid = $wxuser->openid;
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patient/drug?openid={$openid}";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }

    public function getSendContent ($patient) {
        $name = $patient->name;
        $str = "{$name}家长，用药和孩子情况是治疗中的重要参考依据。为了解孩子用药和变化情况，请及时更新用药记录并完成症状评估。点击『详情』完成更新！";
        return $str;
    }

}

// //////////////////////////////////////////////////////

$process = new DrugScaleNoticeLilly_0(__FILE__);
$process->dowork($patient_id);
