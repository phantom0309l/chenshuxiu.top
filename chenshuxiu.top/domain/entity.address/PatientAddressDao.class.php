<?php
/*
 * PatientAddressDao
 */
class PatientAddressDao extends Dao
{
    public static function getByTypePatientid ($type, $patientid) {
        $cond = " and type = :type and patientid = :patientid ";
        $bind = [
            ':type' => $type,
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('PatientAddress', $cond, $bind);
    }
}