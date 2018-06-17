<?php
/*
 * Dg_projectDao
 */
class Dg_projectDao extends Dao
{
    // 判断项目是否存在
    public static function isHave ($title) {
        $cond = " and title = :title ";
        $bind = [];
        $bind[':title'] = $title;

        $dg_project = Dao::getEntityByCond('Dg_project', $cond, $bind);

        if ($dg_project instanceof Dg_project) {
            return true;
        } else {
            return false;
        }
    }
}