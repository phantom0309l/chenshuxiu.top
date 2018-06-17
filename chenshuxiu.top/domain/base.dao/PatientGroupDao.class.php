<?php
/*
 * PatientGroupDao
 */
class PatientGroupDao extends Dao
{
    public static function getByTitle($title) {
        $cond = " and title = :title ";
        $bind = [
            ':title' => $title
        ];

        return Dao::getEntityByCond('PatientGroup', $cond, $bind);
    }
}
