<?php

/*
 * PcardDao
 */
class PcardDao extends Dao
{
    // 名称: getByDoctoridOut_case_no
    // 备注: 根据就诊卡号
    public static function getByDoctoridOut_case_no ($doctorid, $out_case_no) {
        $cond = "AND doctorid=:doctorid AND out_case_no=:out_case_no ";
        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":out_case_no"] = $out_case_no;

        return Dao::getEntityByCond('Pcard', $cond, $bind);
    }

    // 名称: getByPatientidDoctorid
    // 备注: 某患者-某医生-就诊卡 (应该只有一个)
    public static function getByPatientidDoctorid ($patientid, $doctorid) {
        $cond = "AND patientid=:patientid AND doctorid=:doctorid ";
        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":doctorid"] = $doctorid;

        return Dao::getEntityByCond('Pcard', $cond, $bind);
    }

    // 名称: getListByDoctor
    // 备注: 某医生的就诊卡列表
    public static function getListByDoctor (Doctor $doctor) {
        $cond = "AND doctorid=:doctorid
            ORDER BY last_scan_time DESC, id DESC ";

        $bind = [];
        $bind[":doctorid"] = $doctor->id;

        return Dao::getEntityListByCond('Pcard', $cond, $bind);
    }

    // 名称: getListByDoctoridDiseaseid
    // 备注: 某医生-某疾病-就诊卡列表
    public static function getListByDoctoridDiseaseid ($doctorid, $diseaseid) {
        $cond = "AND doctorid=:doctorid AND diseaseid=:diseaseid
            ORDER BY last_scan_time DESC, id DESC ";

        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":diseaseid"] = $diseaseid;

        return Dao::getEntityListByCond('Pcard', $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注: 某患者的就诊卡列表
    public static function getListByPatient (Patient $patient) {
        $cond = "AND patientid=:patientid
            ORDER BY last_scan_time DESC, id DESC ";
        $bind = [];
        $bind[":patientid"] = $patient->id;

        return Dao::getEntityListByCond('Pcard', $cond, $bind);
    }

    // 名称: getListByCreatePatient
    // 备注:
    public static function getListByCreatePatient (Patient $patient) {
        $cond = "AND create_patientid=:patientid
            ORDER BY last_scan_time DESC, id DESC ";
        $bind = [];
        $bind[":patientid"] = $patient->id;

        return Dao::getEntityListByCond('Pcard', $cond, $bind);
    }

    // 名称: getOneByPatientidDiseaseid
    // 备注: 某患者-某疾病-就诊卡(最新扫码的一个) TODO by sjp : 如果一个医生两个疾病怎么办?
    // 修改: 20170419 by sjp 改名字
    public static function getOneByPatientidDiseaseid ($patientid, $diseaseid) {
        $pcards = PcardDao::getListByPatientidDiseaseid($patientid, $diseaseid);
        return array_shift($pcards);
    }

    // 名称: getListByPatientidDiseaseid
    // 备注: 某患者-某疾病-就诊卡列表
    public static function getListByPatientidDiseaseid ($patientid, $diseaseid) {
        $sql = "select a.*
                from pcards a
                inner join doctordiseaserefs b on b.doctorid=a.doctorid
                where a.patientid=:patientid and b.diseaseid=:diseaseid
                order by a.last_scan_time desc, a.id desc";
        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":diseaseid"] = $diseaseid;

        return Dao::loadEntityList('Pcard', $sql, $bind);
    }

    // 名称: getOneByPatientidWxshopid
    // 备注: 某患者-某服务号-就诊卡(最新扫码的一个) TODO by sjp : 按说应该选就诊卡
    // 创建: 20170419 by sjp
    public static function getOneByPatientidWxshopid ($patientid, $wxshopid) {
        $pcards = PcardDao::getListByPatientidWxshopid($patientid, $wxshopid);
        return array_shift($pcards);
    }

    // 名称: getListByPatientidWxshopid
    // 备注: 某患者-某服务号-就诊卡列表
    // 创建: 20170419 by sjp
    public static function getListByPatientidWxshopid ($patientid, $wxshopid) {
        $sql = "select a.*
                from pcards a
                inner join doctorwxshoprefs b on b.doctorid=a.doctorid
                where a.patientid=:patientid and b.wxshopid=:wxshopid
                order by a.last_scan_time desc, a.id desc";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":wxshopid"] = $wxshopid;

        return Dao::loadEntityList('Pcard', $sql, $bind);
    }
}
