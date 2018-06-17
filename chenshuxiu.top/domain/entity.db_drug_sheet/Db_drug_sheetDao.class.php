<?php
/*
 * Db_drug_sheetDao
 */
class Db_drug_sheetDao extends Dao
{
    public static function getByPatientidAndDoctorid($patientid, $doctorid) {
        $cond = " AND patientid = :patientid AND doctorid = :doctorid ORDER BY thedate DESC";
        $bind = [
            ":patientid" => $patientid,
            ":doctorid" => $doctorid,
        ];

        return self::getEntityListByCond("Db_drug_sheet", $cond, $bind);
    }
}