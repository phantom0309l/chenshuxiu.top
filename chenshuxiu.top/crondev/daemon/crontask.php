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
Debug::trace("=====[cron][beg][crontask.php]=====");

$now = date('Y-m-d H:i:s');
$sql = " select id from crontasks where iswait=1 and isdone=0 and plantime<='{$now}'";
$crontaskids = Dao::queryValues($sql);

foreach ($crontaskids as $crontaskid) {
    $unitofwork = BeanFinder::get("UnitOfWork");

    $crontask = CronTask::getById($crontaskid);

    echo "\n {$crontask->id}  开始执行 ";

    $tasktype = $crontask->tasktype;
    $process = new $tasktype();
    $process->dowork($crontask);

    echo " 执行完毕 ";
    $crontask->iswait = 0;
    $crontask->isdone = 1;
    $crontask->donetime = date('Y-m-d H:i:s');

    $unitofwork->commitAndInit();
}

Debug::trace("=====[cron][end][crontask.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
