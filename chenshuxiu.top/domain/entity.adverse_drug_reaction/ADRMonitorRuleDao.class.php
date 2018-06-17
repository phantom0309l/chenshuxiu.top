<?php

/*
 * ADRMonitorRuleDao
 */

class ADRMonitorRuleDao extends Dao
{
    public static function getByMedicineidAndDiseaseidAndMedicineCommonName($medicineid, $diseaseid, $medicine_common_name) {
        $cond = " AND medicineid = :medicineid AND diseaseid = :diseaseid AND medicine_common_name = :medicine_common_name ";
        $bind = [
            ":medicineid" => $medicineid,
            ":diseaseid" => $diseaseid,
            ":medicine_common_name" => $medicine_common_name,
        ];

        return Dao::getEntityByCond("ADRMonitorRule", $cond, $bind);
    }

    public static function getByMedicineidDiseaseid ($medicineid, $diseaseid) {
        $cond = " AND medicineid = :medicineid AND diseaseid = :diseaseid  ";
        $bind = [
            ":medicineid" => $medicineid,
            ":diseaseid" => $diseaseid
        ];

        return Dao::getEntityByCond("ADRMonitorRule", $cond, $bind);
    }
}