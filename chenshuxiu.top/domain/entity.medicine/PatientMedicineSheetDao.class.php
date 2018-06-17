<?php
/*
 * PatientMedicineSheetDao
 */
class PatientMedicineSheetDao extends Dao
{
    // 名称: getByPatientThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientThedate (Patient $patient, $thedate) {
        $cond = 'and patientid=:patientid and thedate=:thedate limit 1';

        $bind = array(
            ':patientid' => $patient->id,
            ':thedate' => $thedate);

        return Dao::getEntityByCond('PatientMedicineSheet', $cond, $bind);
    }

    // 名称: getLastByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientid ($patientid) {
        $cond = 'and patientid=:patientid order by thedate desc limit 1';
        $bind = array(
            ':patientid' => $patientid);

        return Dao::getEntityByCond('PatientMedicineSheet', $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient (Patient $patient) {
        $cond = 'and patientid=:patientid order by thedate desc ';
        $bind = array(
            ':patientid' => $patient->id);

        return Dao::getEntityListByCond('PatientMedicineSheet', $cond, $bind);
    }
}