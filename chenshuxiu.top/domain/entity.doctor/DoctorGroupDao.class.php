<?php
/*
 * DoctorGroupDao
 */
class DoctorGroupDao extends Dao
{
    public static function getByTitle($title) {
        $cond = " and title = :title ";
        $bind = [
            ':title' => $title
        ];

        return Dao::getEntityByCond('DoctorGroup', $cond, $bind);
    }
}
