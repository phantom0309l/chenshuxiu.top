<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-30
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

class Reset_shoppkgbyshoporderid
{

    public function dowork($shopOrderId) {

        $unitofwork = BeanFinder::get("UnitOfWork");
        echo "[shoporder={$shopOrderId}]\n";

        $shopOrder = ShopOrder::getById($shopOrderId);

        $shopPkgs = $shopOrder->getShopPkgs();

        foreach ($shopPkgs as $shopPkg){
            if($shopPkg->is_goodsout) {
                echo "此订单不可重置配送单！[shoppkgid={$shopPkg->id}]已出库！\n";
                return;
            }
            if($shopPkg->is_sendout) {
                echo "此订单不可重置配送单！[shoppkgid={$shopPkg->id}]已发货！\n";
                return;
            }
            if($shopPkg->is_push_erp) {
                echo "此订单不可重置配送单！[shoppkgid={$shopPkg->id}]已推送至erp！\n";
                return;
            }
        }

        // 删除旧的配送单
        foreach ($shopPkgs as $shopPkg) {
            ShopPkgService::deleteShopPkg($shopPkg);
        }

        // 新建default配送单
        ShopPkgService::createDefaultShopPkgAndItemsByShopOrder($shopOrder);

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Reset_shoppkgbyshoporderid.php]=====");

$process = new Reset_shoppkgbyshoporderid();
$process->dowork($shopOrderId);

Debug::trace("=====[cron][end][Reset_shoppkgbyshoporderid.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
