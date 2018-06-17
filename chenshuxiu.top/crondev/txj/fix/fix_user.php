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

class Fix_user
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $user = User::getById(495685348);
        $user->set4lock("patientid", 0);
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_stockitem.php]=====");

$process = new Fix_user();
$process->dowork();

Debug::trace("=====[cron][end][Fix_stockitem.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
