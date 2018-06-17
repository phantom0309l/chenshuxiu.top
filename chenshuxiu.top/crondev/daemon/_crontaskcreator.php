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

$cronprocessid = $argv[1];

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][_crontaskcreator.php {$cronprocessid}]=====");

$process = new CronTaskCreator();
$process->dowork($cronprocessid);

Debug::trace("=====[cron][end][_crontaskcreator.php {$cronprocessid}]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();

class CronTaskCreator
{

    public function dowork ($cronprocessid) {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $cronprocess = CronProcess::getById($cronprocessid);
        if ($cronprocess->status == 0) {
            exit();
        }

        $cronprocess->last_exe_time = date('Y-m-d H:i:s');
        $cronprocess->status = 0;

        $unitofwork->commitAndInit();

        $tasktype = "{$cronprocess->tasktype}" . 'Creator';
        $process = new $tasktype();
        $process->dowork($cronprocess);

        $unitofwork = BeanFinder::get("UnitOfWork");

        $cronprocess = CronProcess::getById($cronprocessid);
        $cronprocess->status = 1;

        $unitofwork->commitAndInit();
    }
}
