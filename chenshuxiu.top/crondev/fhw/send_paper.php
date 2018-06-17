<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

//Debug::$debug = 'Dev';

class Send_paper
{
    public function run()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

//        $this->send(User::getById(10013)->patient);

        $file_path = "patientid.php";
        if (file_exists($file_path)) {
            $file_arr = file($file_path);
            $cnt = count($file_arr);
            for ($i = 0; $i < $cnt; $i++) {//逐行读取文件内容
                $patientid = str_replace("\n", "", $file_arr[$i]);
                $patient = Patient::getById(trim($patientid));
                if (false == $patient instanceof Patient) {
                    continue;
                }

                $this->send($patient);

                echo $i . " / {$cnt} " . $patient->id . " " . $patient->name . "\n";

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        }

        $unitofwork->commitAndInit();
    }

    public function send($patient)
    {
        $wx_uri = Config::getConfig("wx_uri");
        $url = "{$wx_uri}/paper/wenzhen/?papertplid=665354586";

        $first = array(
            "value" => "",
            "color" => "#ff6600");
        $keywords = array(
            array(
                "value" => $patient->name,
                "color" => "#aaa"),
            array(
                "value" => date("Y-m-d H:i:s"),
                "color" => "#aaa"),
            array(
                "value" => "您好，近期有患者反馈：“希望平台提供的开药门诊服务：1. 支持医保报销。2. 可以送药上门”。为更好的给患者提供专业的院外疾病管理服务，我们希望了解您的反馈，请您点击“详情”填写调查问卷，平台会根据大家的反馈提供相应服务。",
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);
    }

    public function getPatientids()
    {
        $sql = "select a.patientid, a.mobile
                from linkmans a
                inner join patients b on b.id = a.patientid
                where b.diseaseid in (8,14,15,19,21) and a.mobile <> '' ";
        $patientid_mobiles = Dao::queryRows($sql);

        $patientidstr = "";
        foreach ($patientid_mobiles as $patientid_mobile) {
            if ($this->isBeijing($patientid_mobile['mobile'])) {
                $patientidstr .= $patientid_mobile['patientid'] . "\n";
            }
        }

        $myfile = fopen("patientid.php", "w") or die("Unable to open file!");
        fwrite($myfile, $patientidstr);
        fclose($myfile);
    }

    public function isBeijing($mobile)
    {
        $list = [
            'tel' => $mobile
        ];

        $obj = FUtil::curlGet("https://tcc.taobao.com/cc/json/mobile_tel_segment.htm", $list, 3);
        $str = mb_convert_encoding($obj, 'UTF-8', 'GBK');
        $arr = explode("province:'", $str);
        $province = trim(explode("',", $arr[1])[0]);

        if ($province == '北京') {
            return true;
        } else {
            return false;
        }
    }
}

$test = new Send_paper();
//$test->getPatientids();
$test->run();

//$myfile = fopen("patientid.php", "w") or die("Unable to open file!");
//$txt = "1Bill Gates\n";
//$txt .= "2Bill Gates\n";
//$txt .= "3Bill Gates\n";
//$txt .= "4Bill Gates\n";
//$txt .= "5Bill Gates\n";
//fwrite($myfile, $txt);
//fclose($myfile);
//
//$file_path = "patientid.php";
//if(file_exists($file_path)) {
//    $file_arr = file($file_path);
//    for ($i = 0; $i < count($file_arr); $i++) {//逐行读取文件内容
//        $patientid = str_replace("\n", "", $file_arr[$i]);
//        echo $patientid . "\n";
//    }
//}


