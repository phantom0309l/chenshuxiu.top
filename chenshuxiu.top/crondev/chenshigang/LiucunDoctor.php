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
    '2016-07-01' => 70,
    '2016-08-01' => 51,
    '2016-09-01' => 41,
    '2016-10-01' => 18,
    '2016-11-01' => 30,
    '2016-12-01' => 18,
    '2017-01-01' => 51,
    '2017-02-01' => 43,
    '2017-03-01' => 106,
    '2017-04-01' => 68,
    '2017-05-01' => 65,
    '2017-06-01' => 160,
    '2017-07-01' => 188,
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
            SELECT COUNT(DISTINCT doctorid) FROM pipes
            WHERE doctorid IN (
                SELECT id FROM doctors WHERE createtime >= '$start' AND createtime < '$end' AND doctorid < 10000
            )
            AND objtype<>'PushMsg'
            AND createtime >= '$s' AND createtime < '$e'";
        $cnt = Dao::queryValue($sql);
        $newCnt = $newcntMap[$start];
        echo round($cnt/$newCnt, 3) * 100, "%, ";
        //echo $cnt, ",";

        $s = $e;
    }
    echo "\n";
}

Debug::flushXworklog();
