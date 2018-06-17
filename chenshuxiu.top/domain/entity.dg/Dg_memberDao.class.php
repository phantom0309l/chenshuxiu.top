<?php
/*
 * Dg_memberDao
 */
class Dg_memberDao extends Dao
{
    public static function isCenterMaster ($dg_centerid, $doctorid) {
        $sql = " select count(*)
            from dg_members
            where dg_centerid = :dg_centerid and doctorid = :doctorid and is_center_master = 1
            ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isProjectMaster ($dg_projectid, $doctorid) {
        $sql = " select count(*)
            from dg_members
            where dg_projectid = :dg_projectid and doctorid = :doctorid and is_project_master = 1
            ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    // 获取某个项目所有负责人
    public static function getMastersByDg_projectid ($dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid and is_project_master = 1";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    //  获取某个中心所有非项目负责人成员
    public static function getNotProjectMastersByDg_centerid ($dg_centerid) {
        $cond = " and dg_centerid = :dg_centerid and is_project_master = 0 ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    // 获取某个中心所有非负责人
    public static function getNotMastersByDg_centerid ($dg_centerid) {
        $cond = " and dg_centerid = :dg_centerid and is_center_master = 0 ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    // 获取某个中心所有负责人
    public static function getMastersByDg_centerid ($dg_centerid) {
        $cond = " and dg_centerid = :dg_centerid and is_center_master = 1 ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    //  获取成员
    public static function getByDg_projectidDoctorid ($dg_projectid, $doctorid) {
        $cond = " and dg_projectid = :dg_projectid and doctorid = :doctorid ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityByCond("Dg_member", $cond, $bind);
    }

    // 获取中心所有医生
    public static function getListByDg_centerid ($dg_centerid) {
        $cond = " and dg_centerid = :dg_centerid ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    public static function getListBy_projectid ($dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    public static function getListBy_projectidDoctorid ($dg_projectid, $doctorid) {
        $cond = " and dg_projectid = :dg_projectid and dg_centerid in (
                select dg_centerid
                from dg_members
                where doctorid = :doctorid and dg_projectid = :projectid
            )";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;
        $bind[':projectid'] = $dg_projectid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond("Dg_member", $cond, $bind);
    }

    public static function getByDg_centeridDoctorid ($dg_centerid, $doctorid) {
        $cond = " and dg_centerid = :dg_centerid and doctorid = :doctorid ";
        $bind = [];
        $bind[':dg_centerid'] = $dg_centerid;
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityByCond("Dg_member", $cond, $bind);
    }
}