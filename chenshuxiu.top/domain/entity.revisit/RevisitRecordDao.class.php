<?php
// RevisitRecordDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class RevisitRecordDao extends Dao
{
    // 名称: getByPatientidDoctorid
    // 备注:获取最后一次就诊
    // 创建:
    // 修改:
    public static function getByPatientidDoctorid ($patientid, $doctorid) {
        $cond = " and patientid = :patientid and doctorid = :doctorid
            order by thedate desc
            limit 1 ";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":doctorid"] = $doctorid;

        return Dao::getEntityByCond("RevisitRecord", $cond, $bind);
    }

    // 名称: getByPatientidDoctoridToday
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidDoctoridToday ($patientid, $doctorid) {
        $cond = " and patientid = :patientid and doctorid = :doctorid and thedate = :thedate ";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":doctorid"] = $doctorid;
        $bind[":thedate"] = date("Y-m-d");

        return Dao::getEntityByCond("RevisitRecord", $cond, $bind);
    }

    // 名称: getByPatientidNotToday_Last
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidNotToday_Last ($patientid) {
        $cond = " and patientid = :patientid and thedate < :thedate ";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":thedate"] = date("Y-m-d");

        return Dao::getEntityByCond("RevisitRecord", $cond, $bind);
    }

    // 名称: getByPatientidThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidThedate ($patientid, $thedate) {
        $cond = " and patientid = :patientid and thedate = :thedate ";

        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":thedate"] = substr($thedate, 0, 10);

        return Dao::getEntityByCond("RevisitRecord", $cond, $bind);
    }

    // 名称: getByPatientidToday
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidToday ($patientid) {
        return self::getByPatientidThedate($patientid, date('Y-m-d'));
    }

    // 名称: getLastThedateByPatientidDoctorid
    // 备注:获取最后一次就诊日期
    // 创建:
    // 修改:
    public static function getLastByPatientidDoctorid ($patientid, $doctorid) {
        $cond = " and patientid = :patientid and doctorid = :doctorid order by thedate desc limit 1 ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityByCond('RevisitRecord', $cond, $bind);
    }

    // 名称: getLastThedateByPatientidDoctorid
    // 备注:获取最后一次就诊日期
    // 创建:
    // 修改:
    public static function getLastThedateByPatientidDoctorid ($patientid, $doctorid) {
        $rr = self::getByPatientidDoctorid($patientid, $doctorid);

        if ($rr instanceof RevisitRecord) {
            if ($rr->thedate != '0000-00-00' && $rr->thedate != '') {
                return $rr->thedate;
            }
        }

        return "未知";
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid) {
        $cond = " and patientid = :patientid order by thedate desc ";
        $bind = [];
        $bind[":patientid"] = $patientid;

        return Dao::getEntityListByCond("RevisitRecord", $cond, $bind);
    }

    // 名称: getListByPatientidDoctorid
    // 备注:获取就诊列表,逆序
    // 创建:
    // 修改:
    public static function getListByPatientidDoctorid ($patientid, $doctorid) {
        $doctor = Doctor::getById($doctorid);

        if (empty($doctor)) {
            Debug::warn("RevisitRecordDao::getListByPatientidDoctorid({$patientid},{$doctorid}); doctor is null;");
            return [];
        }

        // #4130, 协和风湿免疫科, 王迁 也能看 (医生自己和监管的医生)
        $doctorids_str = $doctor->getDoctorIdsStr();

        $cond = " and patientid = :patientid and doctorid in ({$doctorids_str}) order by thedate desc ";
        $bind = [];
        $bind[":patientid"] = $patientid;

        return Dao::getEntityListByCond("RevisitRecord", $cond, $bind);
    }
}
