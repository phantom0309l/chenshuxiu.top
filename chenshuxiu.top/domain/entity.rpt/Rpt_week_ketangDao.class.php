<?php

/*
 * Rpt_week_ketangDao
 */
class Rpt_week_ketangDao extends Dao
{

    protected static $_database = 'statdb';

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($limit = "") {
        $cond = " order by id desc ";

        if ($limit) {
            $limit = intval($limit);
            $cond .= " limit {$limit} ";
        }

        return Dao::getEntityListByCond("Rpt_week_ketang", $cond, [], self::$_database);
    }

    // 名称: getNumBeforeOneday
    // 备注:
    // 创建:
    // 修改:
    public static function getNumBeforeOneday ($enddate) {
        $sql = "select sum(addedcnt) from rpt_week_ketangs where enddate <= :enddate";

        $bind = [];
        $bind[':enddate'] = $enddate;

        return Dao::queryValue($sql, $bind, self::$_database);
    }

    // 名称: getOne
    // 备注:
    // 创建:
    // 修改:
    public static function getOne ($begindate, $enddate) {
        $cond = " AND begindate = :begindate AND enddate = :enddate ";

        $bind = [];
        $bind[':begindate'] = $begindate;
        $bind[':enddate'] = $enddate;

        return Dao::getEntityByCond("Rpt_week_ketang", $cond, $bind, self::$_database);
    }
}
