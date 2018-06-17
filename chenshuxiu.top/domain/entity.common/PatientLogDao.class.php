<?php
// PatientLogDao

class PatientLogDao extends Dao
{
    public static function getLastOneByPatientidAndType($patientid, $type) {
        $cond = " AND patientid = :patientid AND type = :type ORDER BY id DESC ";
        $bind = [
            ':patientid' => $patientid,
            ':type' => $type,
        ];

        return Dao::getEntityByCond('PatientLog', $cond, $bind);
    }
}
