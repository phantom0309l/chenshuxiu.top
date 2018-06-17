<?php
/*
 * PatientMedicineSheetItemDao
 */
class PatientMedicineSheetItemDao extends Dao
{
    // 名称: getByPatientmedicinesheetidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientmedicinesheetidMedicineid ($patientmedicinesheetid, $medicineid) {
        $cond = "and patientmedicinesheetid=:patientmedicinesheetid and medicineid=:medicineid";

        $bind = array(
            ':patientmedicinesheetid' => $patientmedicinesheetid,
            ':medicineid' => $medicineid);

        return Dao::getEntityByCond("PatientMedicineSheetItem", $cond, $bind);
    }

    // 名称: getListByPatientmedicinesheetid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientmedicinesheetid ($patientmedicinesheetid, $needAll = false) {
        if ($needAll) {
            $cond = "and patientmedicinesheetid=:patientmedicinesheetid";
        } else {
            $cond = "and patientmedicinesheetid=:patientmedicinesheetid AND createby <> 'Auditor'";
        }

        $bind = array(
            ':patientmedicinesheetid' => $patientmedicinesheetid);

        return Dao::getEntityListByCond("PatientMedicineSheetItem", $cond, $bind);
    }

    public static function getListByPatientid($patientid) {
        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                ORDER BY a.drug_date DESC";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::loadEntityList("PatientMedicineSheetItem", $sql, $bind);
    }

    // 获取最早的item
    public static function getFirstDrugByPatientidAndMedicineid($patientid, $medicineid) {
        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                AND a.medicineid = :medicineid
                AND a.status != 3
                ORDER BY a.drug_date ASC
                LIMIT 1";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':medicineid'] = $medicineid;
        return Dao::loadEntity("PatientMedicineSheetItem", $sql, $bind);
    }

    // 获取患者正在服用的药
    public static function getTakingListByPatientid($patientid) {
        $sql = "SELECT temp.* 
                FROM (
                    SELECT distinct a.* 
                    FROM patientmedicinesheetitems a 
                    LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid 
                    WHERE b.patientid = :patientid 
                    AND b.doctorid <> 0
                    ORDER BY a.drug_date DESC
                ) temp
                GROUP BY temp.medicineid
                HAVING temp.status <> 3;";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::loadEntityList("PatientMedicineSheetItem", $sql, $bind);
    }

    /**
     * 停药的
     *
     * @param $patientid
     * @param $medicineid
     * @return array
     */
    public static function getLastStopByPatientidAndMedicineid($patientid, $medicineid) {
        $sql = "SELECT a.*
                FROM patientmedicinesheetitems a
                LEFT JOIN patientmedicinesheets b ON b.id = a.patientmedicinesheetid
                WHERE b.patientid = :patientid
                AND a.medicineid = :medicineid
                AND a.status = 3
                ORDER BY a.drug_date DESC
                LIMIT 1";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':medicineid'] = $medicineid;
        return Dao::loadEntityList("PatientMedicineSheetItem", $sql, $bind);
    }
}
