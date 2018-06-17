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
$sql = "SELECT distinct id as patientid FROM patients WHERE doctorid='1423'";

$rows = Dao::queryRows($sql);

if (!$rows) {
    goto end;
}

$mobiles = [];
foreach ($rows as $row) {
    $patientid = $row['patientid'];
    $patient = Patient::getById($patientid);
    $sql = "SELECT id FROM wxusers WHERE wxshopid=" . $wxshopid . " AND patientid=" . $patientid;
    $val = Dao::queryValue($sql);
    if ($val) {
        echo $patient->name, "\t存在wxuser id " . $val, "\n";
    } else {
        $mobile = $patient->getMasterMobile();
        $to = $mobile;
        $mobileDatas[] = [$to, $patientid, $patient->name];
    }
}
//$mobileDatas = [
    ////['13581810831', 1, '李欣宇']//李新宇
    //['18709231977', 1, '陈士岗']//陈士岗
    ////['13521841027', 1, '冯伟']//冯伟
    ////['13811270785', 1, '王宫瑜'],//王宫瑜
//];
//

$lines = file('./guo.csv');

foreach ($lines as $line) {
    $line = trim($line);
    list($id,$patientname,$sex,$mobile1, $mobile2, $mobile3) = explode(',', $line);
    if ($mobile1) {
        $mobileDatas[] = [$mobile1, $id, $patientname];
    }
    if ($mobile2) {
        $mobileDatas[] = [$mobile2, $id, $patientname];
    }
    if ($mobile3) {
        $mobileDatas[] = [$mobile3, $id, $patientname];
    }
}

$tempId = '214366';
$i = 0;
foreach ($mobileDatas as $mobileData) {
    $i ++;
    list($to, $patientid, $patientname) = $mobileData;
    $datas = [
    ];
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

    echo $patientid, "\t", $patientname, "\t", $to, "\n";
}

end:
    $end = (float)(microtime(true) * 1000);
echo "end cost ". round(($end - $begin)/1000, 4) ."s\n";

Debug::flushXworklog();
