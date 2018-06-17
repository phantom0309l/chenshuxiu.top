<?php
/*
 * Rpt_week_doctor_patientDao
 */
class Rpt_week_doctor_patientDao extends Dao
{
    // 名称: getOneByDoctor
    // 备注: 某医生和某周的报表对象
    // 创建:
    // 修改:
    protected static $_database = 'statdb';
    public static function getOneByDoctor ($doctorid, $begindate, $enddate) {
        $cond = "AND doctorid=:doctorid AND begindate=:begindate AND enddate=:enddate ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':begindate'] = $begindate;
        $bind[':enddate'] = $enddate;

        return Dao::getEntityByCond("Rpt_week_doctor_patient", $cond, $bind, self::$_database);
    }
}
