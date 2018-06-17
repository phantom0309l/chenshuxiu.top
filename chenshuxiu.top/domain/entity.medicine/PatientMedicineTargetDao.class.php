<?php
/*
 * PatientMedicineTargetDao
 */
class PatientMedicineTargetDao extends Dao
{
    // 名称: getByPatientMedicine
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientMedicine (Patient $patient, Medicine $medicine, $createby = null) {
        $cond = " and patientid=:patientid and medicineid=:medicineid";
        $bind = array(
            ":patientid" => $patient->id,
            ":medicineid" => $medicine->id);

        if ($createby != null) {
            $cond .= ' and createby=:createby';
            $bind[':createby'] = $createby;
        }

        return Dao::getEntityByCond('PatientMedicineTarget', $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient (Patient $patient, $createby = null) {
        $cond = " and patientid=:patientid ";
        $bind = array(
            ":patientid" => $patient->id);

        if ($createby != null) {
            $cond .= ' and createby=:createby';
            $bind[':createby'] = $createby;
        }

        return Dao::getEntityListByCond('PatientMedicineTarget', $cond, $bind);
    }

    public static function getListByPatientIdAndDoctorId($patientid, $doctorid, $createby = null) {
        $cond = " AND patientid=:patientid AND doctorid=:doctorid";
        $bind = [
            ":patientid" => $patientid,
            ":doctorid" => $doctorid
        ];

        if ($createby != null) {
            $cond .= ' AND createby=:createby';
            $bind[':createby'] = $createby;
        }

        return Dao::getEntityListByCond('PatientMedicineTarget', $cond, $bind);
    }
}
