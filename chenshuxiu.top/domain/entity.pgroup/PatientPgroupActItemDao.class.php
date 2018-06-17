<?php

/*
 * PatientPgroupActItemDao
 */
class PatientPgroupActItemDao extends Dao
{

    // 名称: getListByPatientpgrouprefid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientpgrouprefid ($patientpgrouprefid, $condFix = "") {
        $cond = "AND patientpgrouprefid = :patientpgrouprefid " . $condFix;
        $bind = [];
        $bind[':patientpgrouprefid'] = $patientpgrouprefid;
        return Dao::getEntityListByCond("PatientPgroupActItem", $cond, $bind);
    }

    // 名称: getOneByPatientpgrouprefid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientpgrouprefid ($patientpgrouprefid, $condFix = "") {
        $cond = "AND patientpgrouprefid = :patientpgrouprefid " . $condFix;
        $bind = [];
        $bind[":patientpgrouprefid"] = $patientpgrouprefid;
        return Dao::getEntityByCond("PatientPgroupActItem", $cond, $bind);
    }
}
