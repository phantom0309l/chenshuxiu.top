<?php

/*
 * PatientMedicineRefDao
 */
class PatientMedicineRefDao extends Dao
{

    // 名称: getAllListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getAllListByPatient (Patient $patient, $condFix = "") {
        $cond = "AND patientid = :patientid AND medicineid > 0 {$condFix} ORDER BY status DESC";
        $bind = array(
            ":patientid" => $patient->id);
        return Dao::getEntityListByCond("PatientMedicineRef", $cond, $bind);
    }

    // 名称: getOneByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatient (Patient $patient, $condFix = "") {
        $cond = "AND patientid = :patientid AND medicineid > 0 {$condFix}";
        $bind = array(
            ":patientid" => $patient->id);
        return Dao::getEntityByCond("PatientMedicineRef", $cond, $bind);
    }

    // 名称: getByPatientidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidMedicineid ($patientid, $medicineid) {
        $bind = array(
            ":patientid" => $patientid,
            ":medicineid" => $medicineid);

        return Dao::getEntityByBind("PatientMedicineRef", $bind);
    }

    // 名称: getByPatientMedicine
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientMedicine (Patient $patient, Medicine $medicine) {
        return self::getByPatientidMedicineid($patient->id, $medicine->id);
    }

    // 名称: getCntByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatientid ($patientid, $condFix = "") {
        $sql = " SELECT count(*)
        FROM patientmedicinerefs
        WHERE patientid = :patientid " . $condFix;

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getFirst_start_dateByPatient
    // 备注:真正的首次服药日期
    // 创建:
    // 修改:
    public static function getFirst_start_dateByPatient (Patient $patient) {
        $sql = "SELECT first_start_date
            FROM patientmedicinerefs
            WHERE patientid = :patientid AND medicineid>0 AND first_start_date<>'0000-00-00'
            ORDER BY first_start_date
            LIMIT 1";
        $bind = array(
            ":patientid" => $patient->id);
        return Dao::queryValue($sql, $bind);
    }

    // 名称: getFirst_start_dateOfLevelByPatient
    // 备注:level 为9的首次服药时间 by wgy for app
    // 创建:
    // 修改:
    public static function getFirst_start_dateOfLevelByPatient (Patient $patient, $level = 9) {
        $sql = "SELECT a.first_start_date
            FROM patientmedicinerefs a
            INNER JOIN diseasemedicinerefs b ON a.medicineid = b.medicineid
            WHERE a.patientid = :patientid AND a.medicineid > 0 AND b.level = :level AND a.first_start_date<>'0000-00-00'
            ORDER BY a.first_start_date
            LIMIT 1";
        $bind = array(
            ":patientid" => $patient->id,
            ":level" => $level);
        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient (Patient $patient, $status = 1) {
        $cond = "AND patientid = :patientid AND medicineid > 0 AND status = {$status}";
        $bind = array(
            ":patientid" => $patient->id);
        return Dao::getEntityListByCond("PatientMedicineRef", $cond, $bind);
    }

    // 名称: getMainListByPatient
    // 备注:use for disease 3 by wgy
    // 创建:
    // 修改:
    public static function getMainListByPatient (Patient $patient, $status = 1) {
        $cond = "AND patientid = :patientid AND medicineid > 0 AND status = :status
            AND medicineid in (select medicineid from diseasemedicinerefs where level = 9  ) ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':status'] = $status;

        return Dao::getEntityListByCond("PatientMedicineRef", $cond, $bind);
    }

    // 名称: getNoDrugOneByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getNoDrugOneByPatientid ($patientid) {
        return self::getByPatientidMedicineid($patientid, 0);
    }

    // 名称: getNoStopByPatientidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getNoStopByPatientidMedicineid ($patientid, $medicineid) {
        $bind = array(
            ":patientid" => $patientid,
            ":medicineid" => $medicineid,
            ":status" => 1);

        return Dao::getEntityByBind("PatientMedicineRef", $bind);
    }
}
