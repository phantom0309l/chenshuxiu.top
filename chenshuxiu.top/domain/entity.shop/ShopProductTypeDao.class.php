<?php

/*
 * ShopProductTypeDao
 */

class ShopProductTypeDao extends Dao
{
    public static function getListByDiseaseGroupid($diseasegroupid) {
        $cond = " AND diseasegroupid = :diseasegroupid ";
        $bind = [
            ':diseasegroupid' => $diseasegroupid,
        ];

        return Dao::getEntityListByCond("ShopProductType", $cond, $bind);
    }

    public static function getListByDiseaseGroupids($diseasegroupids) {
        $cond = " AND diseasegroupid IN ({$diseasegroupids}) ";

        return Dao::getEntityListByCond("ShopProductType", $cond);
    }

}