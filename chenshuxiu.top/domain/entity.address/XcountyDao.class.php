<?php
/*
 * XquDao
 */
class XcountyDao extends Dao
{
    public static function getListByXcityid ($xcityid) {
        $cond = " and xcityid = :xcityid order by id asc ";
        $bind = [
            ':xcityid' => $xcityid
        ];

        return Dao::getEntityListByCond('Xcounty', $cond, $bind);
    }
}