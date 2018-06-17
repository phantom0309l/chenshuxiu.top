<?php
/*
 * Rpt_Week_Cancer_DoctorDao
 */
class Rpt_Week_Cancer_DoctorDao extends Dao
{
    public static function getByDoctoridWeekendDate ($doctorid, $weekend_date) {
        $cond = " and doctorid = :doctorid and weekend_date = :weekend_date ";
        $bind = [
            ':doctorid' => $doctorid,
            ':weekend_date' => "{$weekend_date}"
        ];

        return Dao::getEntityByCond('Rpt_Week_Cancer_Doctor', $cond, $bind, 'statdb');
    }
}