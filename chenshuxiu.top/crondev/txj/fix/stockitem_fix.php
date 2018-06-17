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

class Stockitem_fix
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = array(503608496);
        $i = 0;
        foreach ($ids as $id) {
            $shopOrder = ShopOrder::getById($id);

            if(true == $shopOrder->checkStock()){
                //出库
                foreach ($shopOrder->getShopOrderItems() as $shopOrderItem) {
                    $cnt = $shopOrderItem->cnt;
                    if ($cnt < 1) {
                        continue;
                    }
                    ShopProductService::goodsOut($shopOrderItem);
                    //$shopproduct = $shopOrderItem->shopproduct;
                    //$left_cnt = $shopproduct->left_cnt;
                    //$shopproduct->left_cnt = $left_cnt - $cnt;
                }
            }else{
                $str = "因库存不足出库失败";
                echo "\n====[{$id}]因库存不足出库失败===\n";
            }

            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Stockitem_fix.php]=====");

$process = new Stockitem_fix();
$process->dowork();

Debug::trace("=====[cron][end][Stockitem_fix.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
