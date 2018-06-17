<?php
/*
 * XprovinceDao
 */
class XprovinceDao extends Dao{
    public static function getAll () {
        return Dao::getEntityListByCond('Xprovince');
    }
}