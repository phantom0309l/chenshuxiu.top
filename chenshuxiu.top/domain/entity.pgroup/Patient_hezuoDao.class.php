<?php
/*
 * Patient_hezuoDao
 */
class Patient_hezuoDao extends Dao {
    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($condEx="") {
        $cond = " {$condEx}";
        return Dao::getEntityListByCond("Patient_hezuo", $cond);
    }

    // 名称: getOneByCompanyPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByCompanyPatientid ($company, $patientid, $condEx="") {
        $cond = " and company = :company and patientid = :patientid {$condEx}";
        $bind = [];
        $bind[":company"] = $company;
        $bind[":patientid"] = $patientid;
        return Dao::getEntityByCond("Patient_hezuo", $cond, $bind);
    }

    // 名称: getCntByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByCompanyDoctorid ($company, $doctorid, $condEx="") {
        $sql = " select count(a.id) as cnt
            from patient_hezuos a
            inner join patients b on b.id=a.patientid
            where a.company = :company and b.doctorid = :doctorid {$condEx}";
        $bind = [];
        $bind[":company"] = $company;
        $bind[":doctorid"] = $doctorid;
        return Dao::queryValue($sql, $bind);
    }

    // 名称: getPatientidsByCompanyAndStatus
    // 备注:
    // 创建:
    // 修改:
    public static function getPatientidsByCompanyAndStatus ($status, $company) {
        $sql = "SELECT patientid FROM patient_hezuos
                WHERE company=:company AND status=:status";
        $bind = [];
        $bind[':company'] = $company;
        $bind[':status'] = $status;

        return Dao::queryRows($sql,$bind);
    }

}
