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

$start_date = '2017-01-01';
$end_date = '2017-07-19';
$adhd_diseaseids = [1];
$cancer_diseaseids = [8, 15, 19, 21];

//$sql = "SELECT id, createtime FROM patients WHERE createtime >= '$start_date' AND createtime < '$end_date' AND status=1 AND diseaseid IN (".implode(',', $adhd_diseaseids).") AND name NOT LIKE '%测试%'";
$sql = "SELECT id, createtime FROM patients WHERE createtime >= '$start_date' AND createtime < '$end_date' AND status=1 AND diseaseid IN (".implode(',', $cancer_diseaseids).") AND name NOT LIKE '%测试%'";
$patients = Dao::queryRows($sql);
$totalPatient = 0;
$totalActive = 0;
foreach ($patients as $patient) {
    $patientid = $patient['id'];
    $createtime = $patient['createtime'];
    $oneMonthTime = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($createtime)));
    //echo $oneMonthTime, "\n";
    $sql = "SELECT COUNT(*) FROM pipes WHERE patientid='$patientid' AND createtime < '$oneMonthTime' AND objtype <> 'PushMsg'";
    $cnt = Dao::queryValue($sql);
    $totalPatient ++;
    $totalActive += $cnt;
    echo $totalPatient, "\t", $patientid, "\t", $cnt, "\n";
}

echo $totalActive, '/', $totalPatient, "\n";

Debug::flushXworklog();
