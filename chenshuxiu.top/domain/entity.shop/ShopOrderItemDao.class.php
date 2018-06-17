<?php

/*
 * ShopOrderItemDao
 */
class ShopOrderItemDao extends Dao
{
    // 获取某订单下, 所有订单项
    public static function getShopOrderItemsByShopOrder (ShopOrder $shopOrder) {
        $cond = " and shoporderid = :shoporderid ";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::getEntityListByCond('ShopOrderItem', $cond, $bind);
    }

    // 获取某订单下, 某商品的订单项(未必存在)
    public static function getShopOrderItemByShopOrderShopProduct (ShopOrder $shopOrder, ShopProduct $shopProduct) {
        $cond = " and shoporderid = :shoporderid and shopproductid = :shopproductid ";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::getEntityByCond('ShopOrderItem', $cond, $bind);
    }

    // 获取某个产品的所有订单项
    public static function getShopOrderItemCntByShopProduct (ShopProduct $shopProduct) {
        $sql = "select count(*)
                from shoporderitems
                where shopproductid = :shopproductid ";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某个产品的所有已支付订单项
    public static function getShopOrderItemCntOfIs_payByShopProduct (ShopProduct $shopProduct) {
        $sql = "select count(*)
                from shoporderitems a
                inner join shoporders b on b.id = a.shoporderid
                where a.shopproductid = :shopproductid and b.is_pay = 1";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某订单下, 订单项数目
    public static function getShopOrderItemCntByShopOrder (ShopOrder $shopOrder) {
        $sql = "select count(*)
                from shoporderitems
                where shoporderid = :shoporderid ";

        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取某订单下, 商品总数目
    public static function getShopProductSumCntByShopOrder (ShopOrder $shopOrder) {
        $sql = "select sum(cnt)
                from shoporderitems
                where shoporderid = :shoporderid ";

        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取某个商品已支付数量
    public static function getShopProductSumCntOfShopOrderIs_pay (ShopProduct $shopProduct) {
        $sql = "select sum(a.cnt)
                    from shoporderitems a
                    inner join shoporders b on b.id = a.shoporderid
                    where b.is_pay = 1 and a.shopproductid = :shopproductid";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind) + 0;
    }
}
