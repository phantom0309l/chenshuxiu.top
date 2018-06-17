<?php
/*
 * Dc_patientPlanItemDao
 */
class Dc_patientPlanItemDao extends Dao
{
    public static function getListByDc_patientplan (Dc_patientPlan $dc_patientplan) {
        $cond = " and dc_patientplanid = :dc_patientplanid order by plan_date asc ";
        $bind = [
            ':dc_patientplanid' => $dc_patientplan->id
        ];

        return Dao::getEntityListByCond('Dc_patientPlanItem', $cond, $bind);
    }
}