<?php
/*
 * PgroupDao
 */
class PgroupDao extends Dao
{
    // 名称: getListByDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDiseaseid ($diseaseid, $condFix = "") {
        $cond = " and diseaseid = :diseaseid {$condFix}";
        $bind = [];
        $bind[":diseaseid"] = $diseaseid;
        return Dao::getEntityListByCond("Pgroup", $cond, $bind);
    }

    // 名称: getOneByEname
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByEname ($ename) {
        $cond = " and ename = :ename";
        $bind = [];
        $bind[":ename"] = $ename;
        return Dao::getEntityByCond("Pgroup", $cond, $bind);
    }

    // 名称: getOneByEname
    // 备注:
    // 创建:
    // 修改:
    public static function getManageListBySubtypestrAndShowinwx ($subtypestr, $showinwx = 1) {
        $cond = " and typestr = 'manage' and subtypestr = :subtypestr and showinwx = :showinwx order by id asc ";
        $bind = [];
        $bind[':subtypestr'] = $subtypestr;
        $bind[':showinwx'] = $showinwx;
        return Dao::getEntityListByCond('Pgroup', $cond, $bind);
    }
}
