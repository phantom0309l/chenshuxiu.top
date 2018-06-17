<?php
/*
 * Patient_Status_LogDao
 */
class Patient_Status_LogDao extends Dao
{
    // 通过
    public static function getListByPatientid ($patientid) {
        $cond = " and patientid = :patientid order by createtime desc ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond('Patient_Status_Log', $cond, $bind);
    }
}