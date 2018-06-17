<?php

/*
 * XObjLogDao
 */
class XObjLogDao extends Dao
{

    // 获取xobjlog列表
    public static function getListByXunitofworkid ($xunitofworkid) {
        $tableno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $tableno;

        $cond = "and xunitofworkid=:xunitofworkid order by id asc ";
        $bind = [];
        $bind[':xunitofworkid'] = $xunitofworkid;

        return Dao::getEntityListByCond('XObjLog', $cond, $bind, $dbconf);
    }

    // 获取某一个请求新建各类型id
    public static function getObjtypeObjidArrayByXunitofworkid ($xunitofworkid) {
        $tableno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

        $sql = "
            select objtype, group_concat(objid) as objids
            from xobjlogs{$tableno}
            where xunitofworkid = :xunitofworkid and type = 0
            group by objtype";

        $bind = array();
        $bind[':xunitofworkid'] = $xunitofworkid;

        return Dao::queryRows($sql, $bind, 'xworkdb');
    }

    // getListByObjtypeObjid, 正序
    public static function getListByObjtypeObjid ($objtype, $objid) {
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = XObjLog::getTablenoByObjtypeObjid($objtype, $objid);

        $cond = "and objtype = :objtype and objid = :objid order by objver asc, id asc limit 1000";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityListByCond('XObjLog', $cond, $bind, $dbconf);
    }

    // 获取最新的一条记录, 用于修改 objver
    public static function getLastOneByObjtypeObjid ($objtype, $objid) {
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = XObjLog::getTablenoByObjtypeObjid($objtype, $objid);

        $cond = "and objtype = :objtype and objid = :objid order by id desc limit 1 ";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond('XObjLog', $cond, $bind, $dbconf);
    }
}
