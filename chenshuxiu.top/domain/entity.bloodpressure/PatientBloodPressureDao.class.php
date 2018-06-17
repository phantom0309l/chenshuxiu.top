<?php

/*
 * PatientBloodPressureDao
 */

class PatientBloodPressureDao extends Dao
{

    public static function getListByDoctoridAndPatientid($doctorid, $patientid) {
        $cond = ' AND doctorid = :doctorid AND patientid = :patientid ';
        $bind = [
            ':doctorid' => $doctorid,
            ':patientid' => $patientid,
        ];

        return Dao::getEntityListByCond('PatientBloodPressure', $cond, $bind);
    }
}