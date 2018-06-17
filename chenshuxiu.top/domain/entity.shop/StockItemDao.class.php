<?php
/*
 * StockItemDao
 */
class StockItemDao extends Dao {

    // 获取可用于出货的入库单
    public static function getListForGoodsOutByShopProductAndCnt (ShopProduct $shopProduct, $cnt) {

        $stockItems = StockItemDao::getListForGoodsOutByShopProduct($shopProduct);

        $sum = 0;
        $arr = [];

        foreach ($stockItems as $a) {
            // 够用了
            if ($sum >= $cnt) {
                break;
            }

            $sum += $a->left_cnt;
            $arr[] = $a;
        }

        return $arr;
    }

    // 获取还有剩余数量的入库单
    public static function getListForGoodsOutByShopProduct (ShopProduct $shopProduct) {
        $cond = "and shopproductid = :shopproductid and left_cnt > 0 order by id";

        $bind = [];
        $bind[":shopproductid"] = $shopProduct->id;

        return Dao::getEntityListByCond("StockItem", $cond, $bind);
    }

    //获取最近一次的库存记录
    public static function getLastByShopProduct(ShopProduct $shopProduct){
        $cond = "and shopproductid = :shopproductid order by id desc";
        $bind = [];
        $bind[":shopproductid"] = $shopProduct->id;
        return Dao::getEntityByCond("StockItem", $cond, $bind);
    }

    // 获取已出库产品数
    public static function getHasGoodsOutCntByShopProduct (ShopProduct $shopProduct) {
        $sql = "select sum(cnt - left_cnt)
                from stockitems
                where shopproductid = :shopproductid ";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取库存金额
    public static function getSumPriceByShopProduct (ShopProduct $shopProduct) {
        $sql = "select sum(price*left_cnt/100)
                from stockitems
                where shopproductid = :shopproductid ";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    //获取某个商品，在一个时间段内的入库金额
    public static function getAmountByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(price*cnt/100)
                from stockitems
                where shopproductid = :shopproductid and in_time > :startdate and in_time < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryValue($sql, $bind) + 0;
    }

    //获取某个商品，在一个时间段内的入库数量
    public static function getCntByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(cnt)
                from stockitems
                where shopproductid = :shopproductid and in_time > :startdate and in_time < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryValue($sql, $bind) + 0;
    }


}
