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

class Push_Article
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
        $createtime = strtotime($patient_hezuo->createtime);
        $createdate = date("Y-m-d", $createtime);

        $courselessonref = CourseLessonRefDao::getByCourseAndPos($course, $pos);
        $lesson = $courselessonref->lesson;

        $wxuser = $patient->getMasterWxUser(1);
        $openid = $wxuser->openid;

        if ($wxuser instanceof WxUser && $wxuser->subscribe == 1) {
            echo "\n\n--------- " . $wxuser->id;

            $str = "向日葵关爱行动";
            $sendContent = "多动症文章第".$pos."篇：".$lesson->title;
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
            $url = Config::getConfig("wx_uri") . "/lillyarticle/one?openid={$openid}&courselessonrefid={$courselessonref->id}";
            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }

        $unitofwork->commitAndInit();

    }

    private function getLessonPos ($diff) {
        $pos = 0;
        switch ($diff) {
            case '2':
                $pos = 1;
                break;
            case '6':
                $pos = 2;
                break;
            case '10':
                $pos = 3;
                break;
            case '14':
                $pos = 4;
                break;
            case '28':
                $pos = 5;
                break;
            case '56':
                $pos = 6;
                break;
            case '84':
                $pos = 7;
                break;
            case '112':
                $pos = 8;
                break;
            case '140':
                $pos = 9;
                break;
            case '168':
                $pos = 10;
                break;
        }
        return $pos;
    }

}

// //////////////////////////////////////////////////////

$process = new Push_Article(__FILE__);
$cnt = $process->dopush($patientid, $pos);
