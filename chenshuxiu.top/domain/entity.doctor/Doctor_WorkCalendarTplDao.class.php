<?php

/*
 * Doctor_WorkCalendarTplDao
 */

class Doctor_WorkCalendarTplDao extends Dao
{

    public static function getListByDoctorid($doctorid) {
        $cond = ' AND doctorid = :doctorid ';
        $bind = [
            ':doctorid' => $doctorid
        ];

        return Dao::getEntityListByCond('Doctor_WorkCalendarTpl', $cond, $bind);
    }

    public static function getByDoctoridAndDiseaseid($doctorid, $diseaseid) {
        $cond = ' AND doctorid = :doctorid AND diseaseid = :diseaseid ';
        $bind = [
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid,
        ];

        return Dao::getEntityByCond('Doctor_WorkCalendarTpl', $cond, $bind);
    }

}