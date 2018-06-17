<?php

/*
 * ShopProductPictureDao
 */
class ShopProductPictureDao extends Dao
{

    public static function getShopProductPicturesByShopProduct (ShopProduct $shopProduct) {
        $cond = " and shopproductid = :shopproductid order by pos";
        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::getEntityListByCond('ShopProductPicture', $cond, $bind);
    }

    public static function getShopProductPictureCntByShopProduct (ShopProduct $shopProduct) {
        $sql = "select count(*) from shopproductpictures  where shopproductid = :shopproductid ";
        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind);
    }

    public static function getMaxPosByShopProduct (ShopProduct $shopProduct) {
        $sql = "select max(pos) from shopproductpictures  where shopproductid = :shopproductid ";
        $bind = [];
        $bind[':shopproductid'] = $shopProduct->id;

        return Dao::queryValue($sql, $bind);
    }
}