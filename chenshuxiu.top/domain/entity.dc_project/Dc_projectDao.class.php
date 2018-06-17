<?php
/*
 * Dc_projectDao
 */
class Dc_projectDao extends Dao
{
    public static function getByTitle ($title) {
        $cond = " and title = :title ";
        $bind = [
            ':title' => $title
        ];

        return Dao::getEntityByCond('Dc_project', $cond, $bind);
    }
}