<?php

/*
 * PatientMedicineCheckDao
 */

class PatientMedicineCheckDao extends Dao
{
    public static function getListByPatientid($patientid, $condEx = '') {
        if ($condEx) {
            $cond = " and patientid = :patientid {$condEx} ";
        } else {
            $cond = " and patientid = :patientid ";
        }

        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityListByCond('PatientMedicineCheck', $cond, $bind);
    }

    public static function getLastByPatientid($patientid, $condEx = '') {
        if ($condEx) {
            $cond = " AND patientid = :patientid {$condEx} ORDER BY plan_send_date DESC ";
        } else {
            $cond = " AND patientid = :patientid ORDER BY plan_send_date DESC ";
        }

        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('PatientMedicineCheck', $cond, $bind);
    }
}