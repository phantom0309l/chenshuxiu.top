<?php

/*
 * ShopPkgDao
 */

class ShopPkgDao extends Dao
{
    public static function getListByShopOrder(ShopOrder $shopOrder) {
        $cond = " and shoporderid = :shoporderid ";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::getEntityListByCond('ShopPkg', $cond, $bind);
    }

    // 获取配送单 list
    public static function getListByPatient(Patient $patient) {
        $cond = " and patientid = :patientid order by id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('ShopPkg', $cond, $bind);
    }

    // 获取配送单 list
    public static function getListByPatientType(Patient $patient, $type) {
        $sql = "select a.* from shoppkgs a
                inner join shoporders b on b.id=a.shoporderid
                where a.patientid = :patientid and b.type = :type
                order by a.id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::loadEntityList('ShopPkg', $sql, $bind);
    }

    // 获取配送单 list 基于是否发货
    public static function getListByPatientTypeIs_sendout(Patient $patient, $type, $is_sendout) {
        $sql = "select a.* from shoppkgs a
                inner join shoporders b on b.id=a.shoporderid
                where a.patientid = :patientid and b.type = :type and a.is_sendout = :is_sendout
                order by a.id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;
        $bind[':is_sendout'] = $is_sendout;

        return Dao::loadEntityList('ShopPkg', $sql, $bind);
    }

    // 根据快递单号查找shoppkg
    public static function getByExpress_no($express_no) {
        $cond = " and express_no = :express_no";
        $bind = [];
        $bind[':express_no'] = $express_no;

        return Dao::getEntityByCond('ShopPkg', $cond, $bind);
    }

    // 根据shoporder查找shoppkg
    public static function getByShopOrder(ShopOrder $shopOrder) {
        $cond = " and shoporderid = :shoporderid";
        $bind = [];
        $bind[':shoporderid'] = $shopOrder->id;

        return Dao::getEntityByCond('ShopPkg', $cond, $bind);
    }
}