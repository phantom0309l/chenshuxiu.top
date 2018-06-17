<?php

/*
 * BedTktConfigDao
 */

class BedTktConfigDao extends Dao
{
    public static function getAllowListByDoctorid($doctorid) {
        $cond = " AND is_allow_bedtkt = 1 AND doctorid = :doctorid ";
        $bind = [
            ':doctorid' => $doctorid
        ];

        return Dao::getEntityListByCond('BedTktConfig', $cond, $bind);
    }

    public static function getAllowByDoctoridType($doctorid, $typestr) {
        $cond = " AND is_allow_bedtkt = 1 AND doctorid = :doctorid AND typestr = :typestr ";
        $bind = [
            ':doctorid' => $doctorid,
            ':typestr' => $typestr
        ];

        return Dao::getEntityByCond('BedTktConfig', $cond, $bind);
    }

    public static function getByDoctoridType($doctorid, $typestr) {
        $cond = " and doctorid = :doctorid and typestr = :typestr ";
        $bind = [
            ':doctorid' => $doctorid,
            ':typestr' => $typestr
        ];

        return Dao::getEntityByCond('BedTktConfig', $cond, $bind);
    }
}
