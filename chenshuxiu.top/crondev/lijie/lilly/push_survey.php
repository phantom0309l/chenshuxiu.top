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
$pos = $argv[2];

class Push_Survey
{
    public function dopush ($patientid, $pos) {

        $today = date('Y-m-d');

        $unitofwork = BeanFinder::get("UnitOfWork");

        $d = date("Y-m-d", time());
        $courses = CourseDao::getListByGroupstr("lilly");
        $course = $courses[0];

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
        // $patient_hezuo = Patient_hezuo::getById($patient_hezuo_id);
        if(false == $patient_hezuo instanceof Patient_hezuo){
            echo "\n=========没有找到合作患者！";
            return;
        }

        $patient = $patient_hezuo->patient;

        $wxuser = $patient->getMasterWxUser(1);
        $openid = $wxuser->openid;

        if ($wxuser instanceof WxUser && $wxuser->subscribe == 1) {
            echo "\n\n--------- " . $wxuser->id;

            $first = array(
                "value" => "",
                "color" => "");
            $keywords = array(
                array(
                    "value" => "满意度调查问卷",
                    "color" => "#aaa"),
                array(
                    "value" => "在我们所做的服务和您满意的服务之间，差的只是您的意见，请点击『详情』告诉我们，您希望的服务的样子。",
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            if($pos ==1){
                //2月链接
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170533";
            }else {
                //5月链接
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170534";
            }

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }

        $unitofwork->commitAndInit();
        echo "\n=========发送成功！";
    }

}

// //////////////////////////////////////////////////////

$process = new Push_Survey(__FILE__);
$cnt = $process->dopush($patientid, $pos);
