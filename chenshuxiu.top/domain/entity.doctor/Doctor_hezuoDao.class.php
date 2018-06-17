<?php
/*
 * Doctor_hezuoDao
 */
class Doctor_hezuoDao extends Dao {
    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($condEx="") {
        $cond = " {$condEx}";
        return Dao::getEntityListByCond("Doctor_hezuo", $cond);
    }

    // 名称: getOneByCompanyDoctorCode
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByCompanyDoctorCode ($company, $doctor_code, $condEx="") {
        $cond = " and company = :company and doctor_code = :doctor_code {$condEx}";
        $bind = [];
        $bind[":company"] = $company;
        $bind[":doctor_code"] = $doctor_code;
        return Dao::getEntityByCond("Doctor_hezuo", $cond, $bind);
    }

    // 名称: getOneByCompanyDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByCompanyDoctorid ($company, $doctorid, $condEx="") {
        $cond = " and company = :company and doctorid = :doctorid {$condEx}";
        $bind = [];
        $bind[":company"] = $company;
        $bind[":doctorid"] = $doctorid;
        return Dao::getEntityByCond("Doctor_hezuo", $cond, $bind);
    }

}
