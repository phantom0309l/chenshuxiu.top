<?php
/*
 * Rpt_patientDao
 */
class Rpt_week_doctor_dataDao extends Dao
{
    // 名称: getListByDate
    // 备注: 列表 of thedate
    // 创建: by sjp
    // 修改: by sjp
    protected static $_database = 'statdb';

    public static function getByDoctorIdAndDiseaseIdOnWeekend($doctorId, $diseaseId, $weekend_date) {
        $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid AND weekend_date=:weekend_date';
        $bind = [
          ':doctorid' => $doctorId,
          ':diseaseid' => $diseaseId,
          ':weekend_date' => $weekend_date,
        ];
        return Dao::getEntityByCond('Rpt_week_doctor_data', $cond, $bind, self::$_database);
    }

    public static function getListByDoctorIdOnWeekend($doctorId, $weekend_date) {
        $cond = ' AND doctorid=:doctorid AND weekend_date=:weekend_date';
        $bind = [
            ':doctorid' => $doctorId,
            ':weekend_date' => $weekend_date,
        ];
        return Dao::getEntityListByCond('Rpt_week_doctor_data', $cond, $bind, self::$_database);
    }
}
