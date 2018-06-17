<?php

/*
 * AuditorGroupDao
 */

class AuditorGroupDao extends Dao
{
    public static function getByTypeAndEname($type, $ename) {
        $cond = ' AND type = :type AND ename = :ename ';
        $bind = array(
            ':type' => $type,
            ':ename' => $ename,
        );

        return Dao::getEntityByCond('AuditorGroup', $cond, $bind);
    }

    public static function getByName($name) {
        $cond = ' AND name=:name ';
        $bind = array(
            ':name' => $name,
        );

        return Dao::getEntityByCond('AuditorGroup', $cond, $bind);
    }

    public static function getEnamesByType($type){
        $sql = "SELECT ename FROM auditorgroups WHERE type = :type";
        $bind = [
            ':type' => $type
        ];

        return Dao::queryValues($sql, $bind);
    }

    public static function getListByType($type) {
        $cond = ' AND type=:type ';
        $bind = array(
            ':type' => $type,
        );

        return Dao::getEntityListByCond('AuditorGroup', $cond, $bind);
    }
}
