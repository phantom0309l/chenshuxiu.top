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

class Fix_stockitem
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $stockItem = StockItem::getById(706984976);
        //$stockItem->cnt = 2484;
        //$stockItem->left_cnt = 2484;
        $stockItem->set4lock("price",13050);
        //$shopProduct = $stockItem->shopproduct;

        //$left_cnt = $shopProduct->left_cnt;
        //$shopProduct->left_cnt = $left_cnt - 16;
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_stockitem.php]=====");

$process = new Fix_stockitem();
$process->dowork();

Debug::trace("=====[cron][end][Fix_stockitem.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
