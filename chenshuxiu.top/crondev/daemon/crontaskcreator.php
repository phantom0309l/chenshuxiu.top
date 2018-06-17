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

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][crontaskcreator.php]=====");

$nowbefore40s = date('Y-m-d H:i:s', time() - 40);

$cond = " and status=1 and last_exe_time<'{$nowbefore40s}' ";
$cronprocesses = Dao::getEntityListByCond('CronProcess', $cond);

// 配置脚本路径
$filepath = ROOT_TOP_PATH . '/cron/daemon/_crontaskcreator.php';

foreach ($cronprocesses as $cronprocess) {
    $tablearr = array(
        'm' => $cronprocess->m,
        'h' => $cronprocess->h,
        'dom' => $cronprocess->dom,
        'mon' => $cronprocess->mon,
        'dow' => $cronprocess->dow);

    if (false == isNowAccordCrontable($tablearr)) {
        continue;
    }

    exec("php {$filepath} {$cronprocess->id}");

    // sleep(1);
}

function isNowAccordCrontable (Array $tablearr) {
    $m = $tablearr['m'];
    $h = $tablearr['h'];
    $dom = $tablearr['dom'];
    $mon = $tablearr['mon'];
    $dow = $tablearr['dow'];

    if ($mon != '*') {
        if (date('n') != $mon) {
            return false;
        }
    }

    if ($dow != '*') {
        if (date('w') != $dow) {
            return false;
        }
    }

    if ($dom != '*') {
        if (date('j') != $dom) {
            return false;
        }
    }

    if ($h != '*') {
        if ((date('G') != $h) && (date('H') != $h)) {
            return false;
        }
    }

    if ($m != '*') {
        $m = '0' . $m;
        $m = substr($m, - 2, 2);
        if (date('i') != $m) {
            return false;
        }
    }

    return true;
}

Debug::trace("=====[cron][end][crontaskcreator.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
