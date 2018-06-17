<?php
/*
 * CerticanDao
 */
class CerticanDao extends Dao
{
    public static function getListByPatient (Patient $patient) {
        $cond = " and patientid = :patientid order by begin_date ";
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('Certican', $cond, $bind);
    }
}