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

class Init_shopaddresss
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $shopOrder = ShopOrder::getById(607629486);
        $shopaddress = $shopOrder->shopaddress;
        //$shopaddress->xcountyid = 130302;
        //$shopaddress->content = "杜庄镇便捷物流1";
        $shopaddress->linkman_mobile = "13514438059";
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_shopaddresss.php]=====");

$process = new Init_shopaddresss();
$process->dowork();

Debug::trace("=====[cron][end][Init_shopaddresss.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
