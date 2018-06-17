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

$min = '2016-06-01';

$max = '2017-07-01';

$newcntMap = [
    '2016-07-01' => 726,
    '2016-08-01' => 1323,
    '2016-09-01' => 1723,
    '2016-10-01' => 1170,
    '2016-11-01' => 1356,
    '2016-12-01' => 1172,
    '2017-01-01' => 866,
    '2017-02-01' => 1067,
    '2017-03-01' => 1623,
    '2017-04-01' => 1393,
    '2017-05-01' => 1376,
    '2017-06-01' => 1299,
    '2017-07-01' => 1641,
];

while ($min < $max) {
    $start = date('Y-m-d', strtotime('+1 month', strtotime($min)));
    $end = date('Y-m-d', strtotime('+1 month', strtotime($start)));
    $min = $start;
    //echo "out", "\t", $start, "\t", $end, "\n";
    $s = $end;
    $e = "";
    while(true) {
        $e = date('Y-m-d', strtotime('+1 month', strtotime($s)));
        if ($e > '2017-08-01') {
            break;
        }
        //echo "inner", "\t", $s, "\t", $e;
        $sql = "
            SELECT COUNT(DISTINCT patientid) FROM pipes
            WHERE patientid IN (
                SELECT id FROM patients WHERE createtime >= '$start' AND createtime < '$end'
            )
            AND objtype<>'PushMsg'
            AND createtime >= '$s' AND createtime < '$e'";
        $cnt = Dao::queryValue($sql);
        $newCnt = $newcntMap[$start];
        echo round($cnt/$newCnt, 3) * 100, "%, ";

        $s = $e;
    }
    echo "\n";
}

Debug::flushXworklog();
