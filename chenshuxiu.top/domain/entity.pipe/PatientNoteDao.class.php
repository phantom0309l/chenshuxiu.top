<?php
/*
 * PatientNoteDao
 */
class PatientNoteDao extends Dao
{
    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient ( Patient $patient) {

        $bind = [];
        $cond = " AND patientid = :patientid order by id desc ";
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond("PatientNote", $cond, $bind);
    }
}
