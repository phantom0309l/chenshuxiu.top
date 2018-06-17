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

class Fix_xcounty
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $xcounty = Xcounty::getById(510626);
        $xcounty->name = "罗江区";
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_xcounty.php]=====");

$process = new Fix_xcounty();
$process->dowork();

Debug::trace("=====[cron][end][Fix_xcounty.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
