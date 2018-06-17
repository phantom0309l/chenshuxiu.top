<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-03
 * Time: 上午11:44
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$shopOrderId = $argv[1];

class Ceshi_shoporderservice_updateitems
{

    public function dowork($shopOrderId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $shopOrder = ShopOrder::getById($shopOrderId);

        if ($shopOrder instanceof ShopOrder) {
            ShopOrderService::updateItems($shopOrder);
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Ceshi_shoporderservice_updateitems.php]=====");

$process = new Ceshi_shoporderservice_updateitems();
$process->dowork($shopOrderId);

Debug::trace("=====[cron][end][Ceshi_shoporderservice_updateitems.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
