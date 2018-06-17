<?php

/*
 * PatientRecordDao
 */

class PatientRecordDao extends Dao
{
    // 名称: getListByPatientidPatientRecordTplid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientidPatientRecordTplid($patientid, $patientRecordTplid, $asc = true) {
        if ($asc) {
            $cond_order = " order by thedate asc,id asc ";
        } else {
            $cond_order = " order by thedate desc,id desc ";
        }

        $cond = " and patientid=:patientid and patientrecordtplid=:patientrecordtplid and parent_patientrecordid = 0 {$cond_order} ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':patientrecordtplid'] = $patientRecordTplid;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid($patientid, $asc = true) {
        if ($asc) {
            $cond_order = " order by thedate asc,id asc ";
        } else {
            $cond_order = " order by thedate desc,id desc ";
        }

        $cond = " and patientid=:patientid and parent_patientrecordid = 0 {$cond_order} ";
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCodeAndType($code, $type) {
        $cond = " AND code = :code AND type = :type and parent_patientrecordid = 0 ORDER BY thedate ASC,id ASC ";

        $bind = [];
        $bind[':code'] = $code;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getAllListByPatientidCodeType($patientid, $code, $type) {
        $cond = " and patientid=:patientid and code=:code and type=:type order by thedate asc,id asc ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':code'] = $code;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    // 名称: getParentListByPatientidCodeType
    // 备注:
    // 创建:
    // 修改:
    public static function getParentListByPatientidCodeType($patientid, $code, $type) {
        $cond = " AND patientid = :patientid AND code = :code AND type = :type AND parent_patientrecordid = 0 ORDER BY thedate ASC, id ASC ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':code'] = $code;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    // 名称: getChildrenByParentPatientRecordid
    // 备注:
    // 创建:
    // 修改:
    public static function getChildrenByParentPatientRecordid($parent_patientrecordid) {
        $cond = " AND parent_patientrecordid = :parent_patientrecordid ORDER BY createtime ASC ";
        $bind = [];
        $bind[':parent_patientrecordid'] = $parent_patientrecordid;

        return Dao::getEntityListByCond("PatientRecord", $cond, $bind);
    }

    public static function getPatientidTypeThedate($patientid, $type, $thedate) {
        $cond = " and patientid = :patientid and type = :type and thedate = :thedate order by thedate desc limit 1 ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':type'] = $type;
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond('PatientRecord', $cond, $bind);
    }
}
