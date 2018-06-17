<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Fix_shoporderitem_goodsout
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id from shoporderitems a
                inner join shoporders b on b.id = a.shoporderid
                left join shoporderitemstockitemrefs c on c.shoporderitemid = a.id
                where c.id is null and b.is_goodsout=1 and b.time_pay > '2017-08-26 13:00:00' order by b.time_pay";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $shopOrderItem = ShopOrderItem::getById($id);

            $shopOrder = $shopOrderItem->shoporder;
            $shopProduct = $shopOrderItem->shopproduct;

            if(false == $shopOrder->isGoodsOutAll()){
                continue;
            }

            $shopOrderItemStockItemRefs = ShopOrderItemStockItemRefDao::getListByShopOrderItem($shopOrderItem);
            if( count($shopOrderItemStockItemRefs) > 0){
                continue;
            }

            echo "\n[$shopProduct->title][$shopOrderItem->cnt]\n";
            ShopProductService::goodsOut($shopOrderItem);

        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_shoporderitem_goodsout.php]=====");

$process = new Fix_shoporderitem_goodsout();
$process->dowork();

Debug::trace("=====[cron][end][Fix_shoporderitem_goodsout.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
