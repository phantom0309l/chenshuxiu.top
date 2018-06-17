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

// Debug::$debug = 'Dev';

class RemoveOpTaskChecks
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $optaskchecks = OpTaskCheckDao::getList();

        foreach ($optaskchecks as $optaskcheck) {
            $optaskcheck->remove();
        }

        $unitofwork->commitAndInit();
    }



}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5836.php]=====");

$process = new RemoveOpTaskChecks();
$process->dowork();

Debug::trace("=====[cron][end][Output_5836.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
