<?php
/*
 * SimpleSheetTplDao
 */
class SimpleSheetTplDao extends Dao
{
    public static function getByEname ($ename) {
        $cond = " and ename = :ename ";
        $bind = [
            ':ename' => $ename
        ];

        return Dao::getEntityByCond('SimpleSheetTpl', $cond, $bind);
    }
}