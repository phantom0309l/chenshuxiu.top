<?php
/*
 * Actelion_RemindDao
 */
class Actelion_RemindDao extends Dao
{
    public static function getByPatientidPlan_time ($patientid, $plan_time) {
        $plan_time = date('Y-m-d H:i:s', $plan_time);
        $cond = " and patientid = :patientid and plan_time = :plan_time ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':plan_time'] = $plan_time;

        return Dao::getEntityByCond('Actelion_Remind', $cond, $bind);
    }
}