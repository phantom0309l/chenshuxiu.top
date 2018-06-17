<?php

/*
 * DrugItemDao
 */

class DrugItemDao extends Dao
{
    // 名称: getFirstAfterDate
    // 备注:
    // 创建:
    // 修改:
    public static function getFirstAfterDate($patientid, $record_date, $medicineid = null) {
        $cond = "AND patientid = :patientid AND record_date > :record_date ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':record_date'] = $record_date;

        if ($medicineid > 0) {
            $cond .= " AND medicineid = :medicineid ";
            $bind[':medicineid'] = $medicineid;
        }

        $cond .= " ORDER BY record_date ASC, id ASC LIMIT 1";

        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }

    // 名称: getFirstByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getFirstByPatientid($patientid, $medicineid = null) {
        $cond = "AND patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($medicineid > 0) {
            $cond .= " AND medicineid = :medicineid ";
            $bind[':medicineid'] = $medicineid;
        }

        $cond .= " ORDER BY record_date,id LIMIT 1";

        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }

    // 名称: getFirstValidByPatientidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getFirstValidByPatientidMedicineid($patientid, $medicineid, $type = null) {
        $cond = " AND patientid = :patientid AND medicineid = :medicineid ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':medicineid'] = $medicineid;

        if (!is_null($type)) {
            $cond .= " AND type = :type ";
            $bind[':type'] = $type;
        }

        $cond .= " ORDER BY record_date LIMIT 1 ";

        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }

    // 名称: getLastByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientid($patientid, $medicineid = null, $type = null) {
        $cond = " AND patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($medicineid > 0) {
            $cond .= " AND medicineid = :medicineid ";
            $bind[':medicineid'] = $medicineid;
        }

        if (!is_null($type)) {
            $cond .= " AND type = :type ";
            $bind[':type'] = $type;
        }

        $cond .= " ORDER BY record_date DESC,id DESC LIMIT 1";

        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }

    // 名称: getListByPatientidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientidMedicineid($patientid, $medicineid = null) {
        $cond = " AND patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($medicineid > 0) {
            $cond .= " AND medicineid = :medicineid ";
            $bind[':medicineid'] = $medicineid;
        }

        $cond .= " ORDER BY record_date DESC,id DESC";

        return Dao::getEntityListByCond("DrugItem", $cond, $bind);
    }

    // 名称: getListStopByPatientidMedicineid
    // 备注:
    // 创建:
    // 修改:
    public function getListStopByPatientidMedicineid($patientid, $medicineid, $dateBind = null) {
        if (!is_null($dateBind)) {
            $subcond = "AND record_date >= '{$dateBind[0]}' AND record_date <= '{$dateBind[1]}'";
        }
        $cond = "AND patientid = :patientid AND medicineid = :medicineid {$subcond} AND type = 0
                 ORDER BY record_date";
        $bind = array(
            ":patientid" => $patientid,
            ":medicineid" => $medicineid);
        return Dao::getEntityListByCond("DrugItem", $cond, $bind);
    }

    // 名称: getListByDrugsheetid
    // 备注:
    // 创建:
    // 修改:
    public function getListByDrugsheetid($drugsheetid, $condEx = "") {
        $cond = "AND drugsheetid = :drugsheetid {$condEx}";
        $bind = array(
            ":drugsheetid" => $drugsheetid);
        return Dao::getEntityListByCond("DrugItem", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public function getListByPatientid($patientid, $condEx = "") {
        $cond = "AND patientid = :patientid {$condEx}";
        $bind = array(
            ":patientid" => $patientid);
        return Dao::getEntityListByCond("DrugItem", $cond, $bind);
    }

    // 名称: getByPatientid
    // 备注:
    // 创建:
    // 修改:
    public function getByPatientid($patientid, $condEx = "") {
        $cond = "AND patientid = :patientid {$condEx}";
        $bind = array(
            ":patientid" => $patientid);
        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }
}
