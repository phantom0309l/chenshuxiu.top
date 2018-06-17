<?php
/*
 * Rpt_patientDao
 */
class Rpt_patientDao extends Dao
{
    // 名称: getListByDate
    // 备注: 列表 of thedate
    // 创建: by sjp
    // 修改: by sjp
    protected static $_database = 'statdb';
    public static function getListByDate ($thedate, $istest = 0, $condEx = '') {

        $ids = Doctor::getTestDoctorIdStr();

        $condfix = ' and patient_status=1 ';

        if ($istest) {
            $condfix = " and ( doctorid in ({$ids}) or doctorid > 10000 ) ";
        } else {
            $condfix = " and doctorid not in ({$ids}) and doctorid < 10000 ";
        }

        $cond = " and thedate = :thedate and isbaodao = 1 {$condfix} {$condEx} ";
        $bind = array(
            ':thedate' => $thedate);

        return Dao::getEntityListByCond("Rpt_patient", $cond, $bind, self::$_database);
    }

    // 名称: getListByPatient
    // 备注: 列表 of patient
    // 创建: by sjp
    // 修改: by sjp
    public static function getListByPatient ($patientid) {
        $cond = " and patientid = :patientid and patient_status=1 order by id desc ";
        $bind = array(
            ':patientid' => $patientid);

        return Dao::getEntityListByCond("Rpt_patient", $cond, $bind, self::$_database);
    }

    public static function getFirstByPatientidThedateYm ($patientid, $theDateYm) {
        $cond = " and patientid = :patientid and left(thedate, 7)='{$theDateYm}' order by id asc ";
        $bind = array(':patientid' => $patientid);
        return Dao::getEntityByCond("Rpt_patient", $cond, $bind, self::$_database);
    }

    public static function getLastByPatientidThedateYm ($patientid, $theDateYm) {
        $cond = " and patientid = :patientid and left(thedate, 7)='{$theDateYm}' order by id desc ";
        $bind = array(':patientid' => $patientid);
        return Dao::getEntityByCond("Rpt_patient", $cond, $bind, self::$_database);
    }
}
