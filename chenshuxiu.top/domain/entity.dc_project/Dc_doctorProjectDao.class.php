<?php
/*
 * Dc_doctorProjectDao
 */
class Dc_doctorProjectDao extends Dao
{
    public static function getListByDc_project (Dc_project $dc_project) {
        $cond = " and dc_projectid = :dc_projectid order by id desc ";
        $bind = [
            ':dc_projectid' => $dc_project->id
        ];

        return Dao::getEntityListByCond('Dc_doctorProject', $cond, $bind);
    }

    public static function getListByDoctor (Doctor $doctor) {
        $cond = " and doctorid = :doctorid order by id desc ";
        $bind = [
            ':doctorid' => $doctor->id
        ];

        return Dao::getEntityListByCond('Dc_doctorProject', $cond, $bind);
    }
}
