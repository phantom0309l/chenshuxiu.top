<?php

/*
 * MgtGroupDao
 */

class MgtGroupDao extends Dao
{
    // 名称: getByPatientMgtGroupTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientMgtGroupTpl(Patient $patient, MgtGroupTpl $mgtgrouptpl) {
        $cond = " and patientid = :patientid and mgtgrouptplid = :mgtgrouptplid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":mgtgrouptplid"] = $mgtgrouptpl->id;
        return Dao::getEntityByCond("MgtGroup", $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient(Patient $patient) {
        $cond = " and patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        return Dao::getEntityListByCond("MgtGroup", $cond, $bind);
    }

    // 名称: getCntByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatient(Patient $patient) {
        $sql = "select count(*)
                    from mgtgroups
                where patientid = :patientid";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        return Dao::queryValue($sql, $bind);
    }
}