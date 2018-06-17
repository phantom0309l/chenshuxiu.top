<?php
/*
 * ShopProductNoticeDao
 */
class ShopProductNoticeDao extends Dao {

    public static function getByPatientAndShopProduct($patient, $shopproduct, $status = 0) {
        $cond = " and patientid = :patientid and shopproductid = :shopproductid and status = :status ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':shopproductid'] = $shopproduct->id;
        $bind[':status'] = $status;

        return Dao::getEntityByCond('ShopProductNotice', $cond, $bind);
    }

    public static function getListByShopProduct($shopproduct, $status = 0) {
        $cond = " and shopproductid = :shopproductid and status = :status ";

        $bind = [];
        $bind[':shopproductid'] = $shopproduct->id;
        $bind[':status'] = $status;

        return Dao::getEntityListByCond('ShopProductNotice', $cond, $bind);
    }
}