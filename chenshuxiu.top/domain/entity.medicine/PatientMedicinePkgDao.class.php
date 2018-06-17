<?php
// PatientMedicinePkgDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class PatientMedicinePkgDao extends Dao
{
    // 名称: getLastByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientid ($patientid) {
        $cond = ' and patientid=:patientid order by createtime desc limit 1';
        $bind = array(
            ':patientid' => $patientid);

        return Dao::getEntityByCond('PatientMedicinePkg', $cond, $bind);
    }

    // 名称: getLastByPatientidAndDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientidAndDoctorid ($patientid, $doctorid) {
        $cond = ' AND patientid=:patientid AND doctorid=:doctorid ORDER BY createtime DESC LIMIT 1';
        $bind = [
            ':patientid' => $patientid,
            ':doctorid' => $doctorid,
        ];

        return Dao::getEntityByCond('PatientMedicinePkg', $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid) {
        $cond = ' and patientid = :patientid order by createtime desc ';
        $bind = array(
            ':patientid' => $patientid);

        return Dao::getEntityListByCond('PatientMedicinePkg', $cond, $bind);
    }

    // 名称: getPatientmedicineids
    // 备注:
    // 创建:
    // 修改:
    public static function getPatientmedicineids ($patientmedicinepkgid) {
        $arr = array();

        $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkgid);
        foreach ($patientmedicinepkgitems as $a) {
            $arr[] = $a->medicineid;
        }

        return $arr;
    }
}