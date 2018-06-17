<?php
/*
 * Rpt_Day_Cancer_DoctorDao
 */
class Rpt_Day_Cancer_DoctorDao extends Dao
{
    public static function getByDoctoridThedate ($doctorid, $thedate) {
        $cond = " and doctorid = :doctorid and day_date = :thedate ";
        $bind = [
            ':doctorid' => $doctorid,
            ':thedate' => "{$thedate}"
        ];

        return Dao::getEntityByCond('Rpt_Day_Cancer_Doctor', $cond, $bind, 'statdb');
    }
}