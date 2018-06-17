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

class Fix_shopaddress
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $shopAddress = ShopAddress::getById(570882426);
        $shopAddress->content = "北极嘉园小区22号楼3单元4楼右门";
        $shopAddress->xcountyid = "220204";
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_shopaddress.php]=====");

$process = new Fix_shopaddress();
$process->dowork();

Debug::trace("=====[cron][end][Fix_shopaddress.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
