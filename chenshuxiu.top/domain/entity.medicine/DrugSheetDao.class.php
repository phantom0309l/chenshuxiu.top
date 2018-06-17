<?php
/*
 * DrugSheetDao
 */
class DrugSheetDao extends Dao {
    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid, $condEx = "") {
        $cond = " and patientid = :patientid {$condEx}";
        $bind = [];
        $bind[":patientid"] = $patientid;
        return Dao::getEntityListByCond("DrugSheet", $cond, $bind);
    }

    // 名称: getOneByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientid ($patientid, $condEx = "") {
        $cond = " and patientid = :patientid {$condEx}";
        $bind = [];
        $bind[":patientid"] = $patientid;
        return Dao::getEntityByCond("DrugSheet", $cond, $bind);
    }

    // 名称: getOneByPatientidThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientidThedate ($patientid, $thedate) {
        $cond = " and patientid = :patientid and thedate = :thedate";
        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":thedate"] = $thedate;
        return Dao::getEntityByCond("DrugSheet", $cond, $bind);
    }

}
