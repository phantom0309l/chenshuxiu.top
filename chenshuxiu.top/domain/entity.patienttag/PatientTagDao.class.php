<?php
/*
 * PatientTagDao
 */
class PatientTagDao extends Dao
{
    public static function getListByPatientTagTplId ($patienttagtplid) {
        $cond = " and patienttagtplid = :patienttagtplid ";
        $bind = [];
        $bind[':patienttagtplid'] = $patienttagtplid;

        return Dao::getEntityListByCond("PatientTag", $cond, $bind);
    }

    public static function getCntByPatientTagTplId ($patienttagtplid) {
        $patienttags = self::getListByPatientTagTplId($patienttagtplid);

        return count($patienttags);
    }

    public static function getListByPatientidDoctorid ($patientid, $doctorid, $size = 100) {
        $cond = " and patientid = :patientid and doctorid = :doctorid limit {$size}";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond("PatientTag", $cond, $bind);
    }

    public static function getListByPatientid ($patientid, $size = 100) {
        $cond = " and patientid = :patientid limit {$size}";
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("PatientTag", $cond, $bind);
    }
}