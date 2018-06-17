<?php

/*
 * WxGroupDao
 */
class WxGroupDao extends Dao
{

    // 名称: getAllList
    // 备注:
    // 创建:
    // 修改:
    public static function getAllList () {
        return Dao::getEntityListByCond("WxGroup");
    }

    // 名称: getOneByWxshopidEname
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByWxshopidEname ($wxshopid, $ename) {
        $cond = " and wxshopid = :wxshopid and ename = :ename";

        $bind = [];
        $bind[":wxshopid"] = $wxshopid;
        $bind[":ename"] = $ename;

        return Dao::getEntityByCond("WxGroup", $cond, $bind);
    }

    // 名称: getOneByWxshopidGroupid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByWxshopidGroupid ($wxshopid, $groupid) {
        $cond = " and wxshopid = :wxshopid and groupid = :groupid";

        $bind = [];
        $bind[":wxshopid"] = $wxshopid;
        $bind[":groupid"] = $groupid;

        return Dao::getEntityByCond("WxGroup", $cond, $bind);
    }
}
