<?php

/*
 * ReportDao
 */

class ReportDao extends Dao
{
    protected static $entityName = 'Report';

    public static function getListByPatientid($patientid) {
        $cond = ' AND patientid=:patientid ORDER BY createtime DESC ';
        $bind = [
            ':patientid' => $patientid
        ];
        return Dao::getEntityListByCond(self::$entityName, $cond, $bind);
    }

    public static function getListByPatientidAndDoctorid($patientid, $doctorid) {
        $cond = ' AND patientid=:patientid AND doctorid=:doctorid ORDER BY createtime DESC ';
        $bind = [
            ':patientid' => $patientid,
            ':doctorid' => $doctorid,
        ];
        return Dao::getEntityListByCond(self::$entityName, $cond, $bind);
    }
}