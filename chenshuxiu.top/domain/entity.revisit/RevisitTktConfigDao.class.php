<?php
/*
 * RevisitTktConfigDao
 */
class RevisitTktConfigDao extends Dao
{
    public static function getByDoctorDisease (Doctor $doctor, Disease $disease) {
        $cond = " and doctorid = :doctorid and diseaseid = :diseaseid ";

        $bind = [
            ":doctorid" => $doctor->id,
            ':diseaseid' => $disease->id
        ];

        return Dao::getEntityByCond("RevisitTktConfig", $cond, $bind);
    }
}
