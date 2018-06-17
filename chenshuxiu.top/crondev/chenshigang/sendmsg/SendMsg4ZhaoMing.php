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

//echo "发送测试短信";
//$ret = ShortMsg::sendTemplateSMS_j4now(18709231977, ["ca肿瘤管理", "12345"], "62033");
//print_r($ret);
//exit;

############################################

$begin = (float)(microtime(true) * 1000);

$wxshopid = 12;
$sql = "SELECT distinct id as patientid  FROM patients WHERE doctorid='809'";

$rows = Dao::queryRows($sql);

if (!$rows) {
    goto end;
}

$i = 0;
foreach ($rows as $row) {
    $patientid = $row['patientid'];
    $patient = Patient::getById($patientid);
    $sql = "SELECT id FROM wxusers WHERE wxshopid=" . $wxshopid . " AND patientid=" . $patientid;
    $val = Dao::queryValue($sql);
    if ($val) {
        echo $patient->name, "\t存在wxuser id " . $val, "\n";
    } else {
        $i ++;
        $mobile = $patient->getMasterMobile();
        $to = $mobile;
        //$to = '18709231977';
        //$to = '13521841027';//冯伟
        //$to = '13811270785';//王宫瑜
        $datas = [
        ];
        $tempId = "206113";

        $ret = ShortMsg::sendTemplateSMS_j4now($to,  $datas, $tempId);
        echo $i . "\t";
        if ($ret == NULL) {
            echo "发送错误". "\t";
        } else {
            if ($ret->statusCode != 0) {
                echo "发送失败". "\t". $ret->statusCode . "\t". $ret->statusMsg. "\t"; 
            } else {
                echo "发送成功". "\t";
            }
        }

        echo $patientid, "\t", $patient->name, "\t", $to, "\n";

    }

}

end:
$end = (float)(microtime(true) * 1000);
echo "end cost ". round(($end - $begin)/1000, 4) ."s\n";

Debug::flushXworklog();
