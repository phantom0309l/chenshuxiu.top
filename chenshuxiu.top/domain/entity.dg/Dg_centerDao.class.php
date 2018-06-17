<?php
/*
 * Dg_centerDao
 */
class Dg_centerDao extends Dao
{
    // 获取某个项目的所有中心
    public static function getListByDg_projectid ($dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityListByCond('Dg_center', $cond, $bind);
    }

    // 获取当前医生某个项目的所有参与中心
    public static function getListByDg_projectidDoctorid ($dg_projectid, $doctorid) {
        $cond = " and dg_projectid = :dg_projectid and id in (
                select dg_centerid
                from dg_members
                where doctorid = :doctorid
            )";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('Dg_center', $cond, $bind);
    }

    // 判断中心是否存在
    public static function isHave ($dg_projectid, $title) {
        $cond = " and title = :title and dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':title'] = $title;
        $bind[':dg_projectid'] = $dg_projectid;

        $dg_center = Dao::getEntityByCond('Dg_center', $cond, $bind);

        if ($dg_center instanceof Dg_center) {
            return true;
        } else {
            return false;
        }
    }

    // 获取当前医生所在的中心
    public static function getByDg_projectidDoctorid ($dg_projectid, $doctorid) {
        $cond = " and  id = (
                select dg_centerid
                from dg_members
                where doctorid = :doctorid and dg_projectid = :dg_projectid
            )";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityByCond('Dg_center', $cond, $bind);
    }
}