<?php
/*
 * ShopOrderPictureDao
 */
class ShopOrderPictureDao extends Dao {

    // 获取list
    public static function getListByShopOrder (ShopOrder $shopOrder) {
        $cond = " and shoporderid = :shoporderid order by id desc";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::getEntityListByCond('ShopOrderPicture', $cond, $bind);
    }

    // 获取list
    public static function getListByShopOrderType (ShopOrder $shopOrder, $type) {
        $cond = " and shoporderid = :shoporderid and type = :type order by id desc";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond('ShopOrderPicture', $cond, $bind);
    }

    // 获取one
    public static function getOneByShopOrderPicture (ShopOrder $shopOrder, Picture $picture) {
        $cond = " and shoporderid = :shoporderid and pictureid = :pictureid order by id desc";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;
        $bind[':pictureid'] = $picture->id;

        return Dao::getEntityByCond('ShopOrderPicture', $cond, $bind);
    }

}
