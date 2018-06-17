<?php
/*
 * XcityDao
 */
class XcityDao extends Dao
{
    public static function getListByXprovinceid ($xprovinceid) {
        $cond = " and xprovinceid = :xprovinceid order by id asc ";
        $bind = [
            ':xprovinceid' => $xprovinceid
        ];

        return Dao::getEntityListByCond('Xcity', $cond, $bind);
    }
}