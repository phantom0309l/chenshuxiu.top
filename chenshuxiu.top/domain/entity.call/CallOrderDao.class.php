<?php

/*
 * CallOrderDao
 */

class CallOrderDao extends Dao
{

    public static function getAll() {
        return Dao::getEntityListByCond("CallOrder");
    }

    public static function getLastByPatientidAndDoctorid($patientid, $doctorid) {
        $cond = " AND patientid = :patientid AND the_doctorid = :doctorid ORDER BY id DESC";
        $bind = [
            ":patientid" => $patientid,
            ":doctorid" => $doctorid,
        ];

        return Dao::getEntityByCond("CallOrder", $cond, $bind);
    }

}