<?php

/*
 * ServiceProductDao
 */

class ServiceProductDao extends Dao
{
    public static function getList($pagesize, $pagenum) {
        $cond = "";
        $bind = [];
        return Dao::getEntityListByCond4Page("ServiceProduct", $pagesize, $pagenum, $cond, $bind);
    }

    public static function getValidListByType($type) {
        $cond = " AND type = :type AND status = 1 ";
        $bind = [
            ':type' => $type
        ];
        return Dao::getEntityListByCond("ServiceProduct", $cond, $bind);
    }

}