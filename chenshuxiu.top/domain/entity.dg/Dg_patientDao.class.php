<?php
/*
 * Dg_patientDao
 */
class Dg_patientDao extends Dao
{
    // 获取某个项目所有患者
    public static function getListByDg_projectid ($dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityListByCond("Dg_patient", $cond, $bind);
    }

    // 获取某个项目单个患者
    public static function getByPatientidDg_projectid ($patientid, $dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid and patientid = :patientid";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityByCond("Dg_patient", $cond, $bind);
    }
}