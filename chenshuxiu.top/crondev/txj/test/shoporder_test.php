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

class Shoporder_test
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $shopOrder = ShopOrder::getById(521966156);
        $shopOrder->createShopOrderOpTask();

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Shoporder_test.php]=====");

$process = new Shoporder_test();
$process->dowork();

Debug::trace("=====[cron][end][Shoporder_test.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
