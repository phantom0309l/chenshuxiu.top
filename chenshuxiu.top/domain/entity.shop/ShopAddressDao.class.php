<?php

/*
 * ShopAddressDao
 */
class ShopAddressDao extends Dao
{

    public static function getShopAddresssByPatient (Patient $patient) {
        $cond = " and patientid = :patientid order by is_master desc, id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('ShopAddress', $cond, $bind);
    }

    //获取主地址
    public static function getMasterShopAddressByPatient (Patient $patient) {
        $cond = " and patientid = :patientid order by is_master desc, id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityByCond('ShopAddress', $cond, $bind);
    }
}
