<?php
/*
 * ShopOrderItemStockItemRefDao
 */
class ShopOrderItemStockItemRefDao extends Dao {

    // 获取shoporderitem已退货数量
    public static function getHasGoodsBackCntByShopOrderItem (ShopOrderItem $shopOrderItem) {
        $sql = "select sum(cnt)
                from shoporderitemstockitemrefs
                where shoporderitemid = :shoporderitemid and cnt < 0";

        $bind = [];
        $bind[':shoporderitemid'] = $shopOrderItem->id;

        return (0 - Dao::queryValue($sql, $bind));
    }

    // 获取shoporderitem实际出库数量
    public static function getHasGoodsOutCntByShopOrderItem (ShopOrderItem $shopOrderItem) {
        $sql = "select sum(cnt)
                from shoporderitemstockitemrefs
                where shoporderitemid = :shoporderitemid";

        $bind = [];
        $bind[':shoporderitemid'] = $shopOrderItem->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取已出货数量
    public static function getHasGoodsOutCntByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(a.cnt)
                from shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                inner join shoporders c on c.id = b.shoporderid
                inner join shopproducts d on d.id = b.shopproductid
                where d.id = :shopproductid and c.time_pay >= :startdate and c.time_pay < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return 0 + Dao::queryValue($sql, $bind);
    }

    // 获取已出货销售金额
    public static function getHasGoodsOutSaledAmountByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(cast(a.cnt as signed)*cast(b.price as signed)/100)
                from shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                inner join shoporders c on c.id = b.shoporderid
                inner join shopproducts d on d.id = b.shopproductid
                where d.id = :shopproductid and c.time_pay >= :startdate and c.time_pay < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return 0 + Dao::queryValue($sql, $bind);
    }

    // 获取已出货成本金额
    public static function getHasGoodsOutCostAmountByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(cast(a.cnt as signed)*cast(e.price as signed)/100)
                from shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                inner join shoporders c on c.id = b.shoporderid
                inner join shopproducts d on d.id = b.shopproductid
                inner join stockitems e on e.id = a.stockitemid
                where d.id = :shopproductid and c.time_pay >= :startdate and c.time_pay < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return 0 + Dao::queryValue($sql, $bind);
    }

    // 获取已出货产品的 数量、销售金额、成本金额 销售概况
    public static function getHasGoodsOutSaledProfileByShopProductStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select
                    sum(cast(a.cnt as signed)) as cnt,
                    sum(cast(a.cnt as signed)*cast(b.price as signed)/100) as saled_amount,
                    sum(cast(a.cnt as signed)*cast(e.price as signed)/100) as cost_amount
                from shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                inner join shoporders c on c.id = b.shoporderid
                inner join shopproducts d on d.id = b.shopproductid
                inner join stockitems e on e.id = a.stockitemid
                where d.id = :shopproductid and c.time_pay >= :startdate and c.time_pay < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryRow($sql, $bind);
    }



    // 获取订单列表
    public static function getListByShopOrderItem (ShopOrderItem $shopOrderItem) {
        $cond = " and shoporderitemid = :shoporderitemid";
        $bind = [];
        $bind[':shoporderitemid'] = $shopOrderItem->id;

        return Dao::getEntityListByCond('ShopOrderItemStockItemRef', $cond, $bind);
    }

    // 获取订单列表
    public static function getListByShopOrderItemStockItem (ShopOrderItem $shopOrderItem, StockItem $stockItem) {
        $cond = " and shoporderitemid = :shoporderitemid and stockitemid = :stockitemid";
        $bind = [];
        $bind[':shoporderitemid'] = $shopOrderItem->id;
        $bind[':stockitemid'] = $stockItem->id;

        return Dao::getEntityListByCond('ShopOrderItemStockItemRef', $cond, $bind);
    }

}
