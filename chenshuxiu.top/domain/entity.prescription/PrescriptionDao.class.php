<?php

/*
 * PrescriptionDao
 */
class PrescriptionDao extends Dao
{

    public static function getPrescriptionByShopOrder (ShopOrder $shoporder) {
        $cond = " and shoporderid = :shoporderid ";
        $bind = [];
        $bind[':shoporderid'] = $shoporder->id;
        return Dao::getEntityByCond('Prescription', $cond, $bind);
    }
}