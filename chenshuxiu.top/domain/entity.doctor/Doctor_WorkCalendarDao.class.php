<?php

/*
 * Doctor_WorkCalendarDao
 */

class Doctor_WorkCalendarDao extends Dao
{

    public static function getByDoctoridAndPatientid($doctorid, $patientid) {
        $cond = ' AND doctorid = :doctorid AND patientid = :patientid ';
        $bind = [
            ':doctorid' => $doctorid,
            ':patientid' => $patientid,
        ];

        return Dao::getEntityByCond('Doctor_WorkCalendarTpl', $cond, $bind);
    }

    public static function getListByDoctoridAndPatientid($doctorid, $patientid) {
        $cond = " AND doctorid = :doctorid AND patientid = :patientid ORDER BY thedate DESC";
        $bind = [
            ":doctorid" => $doctorid,
            ":patientid" => $patientid,
        ];

        return Dao::getEntityListByCond("Doctor_WorkCalendar", $cond, $bind);
    }

    public static function getListByDoctoridAndPatientidAndRange($doctorid, $patientid, $fromdate, $todate) {
        $cond = " AND doctorid = :doctorid AND patientid = :patientid AND thedate >= :fromdate AND thedate < :todate ORDER BY thedate DESC";
        $bind = [
            ":doctorid" => $doctorid,
            ":patientid" => $patientid,
            ":fromdate" => $fromdate,
            ":todate" => $todate,
        ];

        return Dao::getEntityListByCond("Doctor_WorkCalendar", $cond, $bind);
    }

    public static function getListByDoctoridAndRange($doctorid, $fromdate, $todate) {
        $cond = " AND doctorid = :doctorid AND thedate >= :fromdate AND thedate < :todate AND patientid > 0 ORDER BY thedate DESC";
        $bind = [
            ":doctorid" => $doctorid,
            ":fromdate" => $fromdate,
            ":todate" => $todate,
        ];

        return Dao::getEntityListByCond("Doctor_WorkCalendar", $cond, $bind);
    }

    public static function getByDoctoridAndPatientidAndThedate($doctorid, $patientid, $thedate) {
        $cond = " AND doctorid = :doctorid AND patientid = :patientid AND thedate = :thedate ";
        $bind = [
            ":doctorid" => $doctorid,
            ":patientid" => $patientid,
            ":thedate" => $thedate,
        ];

        return Dao::getEntityByCond("Doctor_WorkCalendar", $cond, $bind);
    }

}