<?php
/*
 * Dc_patientPlanDao
 */
class Dc_patientPlanDao extends Dao
{
    public static function getListByPatient (Patient $patient) {
        $cond = " and patientid = :patientid order by id ";
        $bind = [
            ':patientid' => $patient->id
        ];

        return Dao::getEntityListByCond('Dc_patientPlan', $cond, $bind);
    }

    public static function getListByDc_doctorproject (Dc_doctorProject $dc_doctorproject) {
        $cond = " and dc_doctorprojectid = :dc_doctorprojectid order by id ";
        $bind = [
            ':dc_doctorprojectid' => $dc_doctorproject->id
        ];

        return Dao::getEntityListByCond('Dc_patientPlan', $cond, $bind);
    }

    public static function getByPatientDc_doctorprojectBegin_date (Patient $patient, Dc_doctorProject $dc_doctorproject, $begin_date) {
        $cond = " and patientid = :patientid and dc_doctorprojectid = :dc_doctorprojectid and begin_date <= :begin_date and end_date >= :begin_date ";
        $bind = [
            ':patientid' => $patient->id,
            ':dc_doctorprojectid' => $dc_doctorproject->id,
            ':begin_date' => $begin_date
        ];

        return Dao::getEntityByCond('Dc_patientPlan', $cond, $bind);
    }
}