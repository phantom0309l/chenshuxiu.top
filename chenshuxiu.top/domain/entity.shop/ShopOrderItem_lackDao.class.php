<?php

/*
 * ShopOrderItem_lackDao
 */

class ShopOrderItem_lackDao extends Dao
{
    // 获取某订单下, 某商品的订单(缺货)项(未必存在)
    public static function getByShopOrderShopProduct(ShopOrder $shopOrder, ShopProduct $shopProduct) {
        $cond = " and shoporderid = :shoporderid and shopproductid = :shopproductid ";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::getEntityByCond('ShopOrderItem_lack', $cond, $bind);
    }

    // 获取某订单下, 所有订单项
    public static function getListByShopOrder(ShopOrder $shopOrder) {
        $cond = " and shoporderid = :shoporderid ";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::getEntityListByCond('ShopOrderItem_lack', $cond, $bind);
    }
}