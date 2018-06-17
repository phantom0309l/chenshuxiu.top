<?php

/*
 * MgtGroupTplDao
 */

class MgtGroupTplDao extends Dao
{
    // 名称: getByEname
    // 备注:
    // 创建:
    // 修改:
    public static function getByEname($ename) {
        $cond = " and ename = :ename";
        $bind = [];
        $bind[":ename"] = $ename;
        return Dao::getEntityByCond("MgtGroupTpl", $cond, $bind);
    }

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList() {
        return Dao::getEntityListByCond("MgtGroupTpl");
    }

}