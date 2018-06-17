<?php

/*
 * ShopPkgItemDao
 */

class ShopPkgItemDao extends Dao
{
    // 获取某配送单下, 所有配送单项
    public static function getListByShopPkg (ShopPkg $shopPkg) {
        $cond = " and shoppkgid = :shoppkgid ";
        $bind = [];
        $bind[':shoppkgid'] = $shopPkg->id;

        return Dao::getEntityListByCond('ShopPkgItem', $cond, $bind);
    }

    // 获取某配送单下, 某商品的配送单项(未必存在)
    public static function getByShopPkgShopProduct (ShopPkg $shopPkg, ShopProduct $shopProduct) {
        $cond = " and shoppkgid = :shoppkgid and shopproductid = :shopproductid ";
        $bind = [];
        $bind[':shoppkgid'] = $shopPkg->id;
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::getEntityByCond('ShopPkgItem', $cond, $bind);
    }

    // 获取某订单、某商品下的所有配送单中的数量和
    public static function getCntByShopOrderShopProduct (ShopOrder $shopOrder, ShopProduct $shopProduct) {
        $sql = "SELECT SUM(a.cnt) FROM shoppkgitems a
                INNER JOIN shoppkgs b ON b.id=a.shoppkgid
                WHERE b.shoporderid = :shoporderid AND a.shopproductid = :shopproductid";

        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取某个商品已支付未出库的数量
    public static function getShopProductSumCntOfShopOrderIs_payNotgoodsout (ShopProduct $shopProduct) {
        $sql = "select sum(a.cnt)
                    from shoppkgitems a
                    inner join shoppkgs b on b.id = a.shoppkgid
                    inner join shoporders c on c.id = b.shoporderid
                    where c.is_pay = 1 and b.is_goodsout = 0 and c.refund_amount < c.amount
                    and a.shopproductid = :shopproductid";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取某个商品已支付未出库的数量，在某段时间内
    public static function getShopProductSumCntOfShopOrderIs_payNotgoodsoutByStartdateEnddate (ShopProduct $shopProduct, $startdate, $enddate) {
        $sql = "select sum(a.cnt)
                    from shoppkgitems a
                    inner join shoppkgs b on b.id = a.shoppkgid
                    inner join shoporders c on c.id = b.shoporderid
                    where c.is_pay = 1 and b.is_goodsout = 0 and c.refund_amount < c.amount
                    and a.shopproductid = :shopproductid and c.time_pay >= :startdate and c.time_pay < :enddate";

        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryValue($sql, $bind) + 0;
    }
}