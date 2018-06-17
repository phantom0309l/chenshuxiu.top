<?php
/*
 * PatientCollectionDao
 */
class PatientCollectionDao extends Dao
{
    public static function getByPatientidType ($patientid, $type) {
        $cond = " and patientid = :patientid and type = :type ";
        $bind = [
            ':patientid' => $patientid,
            ':type' => $type
        ];

        return Dao::getEntityByCond('PatientCollection', $cond, $bind);
    }
}