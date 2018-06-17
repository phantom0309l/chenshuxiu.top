<?php
/*
 * OpTaskFilterDao
 */
class OpTaskFilterDao extends Dao
{
    public static function getPublicList () {
        $cond = " and is_public = 1 order by title asc ";
        return Dao::getEntityListByCond('OpTaskFilter' , $cond);
    }

    public static function getPrivateListByCreateauditorid ($create_auditorid) {
        $cond = " and create_auditorid = :create_auditorid and is_public = 0 order by title asc ";
        $bind = [
            ':create_auditorid' => $create_auditorid
        ];

        return Dao::getEntityListByCond('OpTaskFilter', $cond, $bind);
    }

    public static function getListByCreateauditorid ($create_auditorid) {
        $cond = " and create_auditorid = :create_auditorid order by title asc ";
        $bind = [
            ':create_auditorid' => $create_auditorid
        ];

        return Dao::getEntityListByCond('OpTaskFilter', $cond, $bind);
    }
}