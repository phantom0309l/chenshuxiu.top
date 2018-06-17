<?php

/*
 * XerrlogDao
 */
class XerrlogDao extends Dao
{

    // 获取Xerrlog列表
    public static function getListByXunitofworkid ($xunitofworkid) {
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';

        $cond = "and xunitofworkid=:xunitofworkid order by id asc ";
        $bind = [];
        $bind[':xunitofworkid'] = $xunitofworkid;

        return Dao::getEntityListByCond('Xerrlog', $cond, $bind, $dbconf);
    }

    // getListByObjtypeObjid, 正序
    public static function getListByStatus ($status = 0) {
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';

        $cond = "and status=:status order by id asc limit 1000";
        $bind = [];
        $bind[':status'] = $status;

        return Dao::getEntityListByCond('Xerrlog', $cond, $bind, $dbconf);
    }
}
