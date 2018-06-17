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

class Fix_is_push_erp
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $shopOrder = ShopOrder::getById(738765276);
        $shopOrder->is_push_erp = 1;
        $shopOrder->time_push_erp = date("Y-m-d H:i:s");
        $shopOrder->remark_push_erp = "";
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_is_push_erp.php]=====");

$process = new Fix_is_push_erp();
$process->dowork();

Debug::trace("=====[cron][end][Fix_is_push_erp.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
