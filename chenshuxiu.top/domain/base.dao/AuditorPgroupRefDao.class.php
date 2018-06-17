<?php

/*
 * AuditorPgroupRefDao
 */
class AuditorPgroupRefDao extends Dao
{

    // getListByAuditorid
    public static function getListByAuditorid ($auditorid) {
        $cond = " and auditorid = :auditorid and status = 1 ";

        $bind = [];
        $bind[':auditorid'] = $auditorid;

        return Dao::getEntityListByCond("AuditorPgroupRef", $cond, $bind);
    }

    // 名称: getOneByAuditoridPgroupid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByAuditoridPgroupid ($auditorid, $pgroupid, $condEx = "") {
        $cond = " and auditorid = :auditorid and pgroupid = :pgroupid {$condEx}";
        $bind = [];
        $bind[":auditorid"] = $auditorid;
        $bind[":pgroupid"] = $pgroupid;
        return Dao::getEntityByCond("AuditorPgroupRef", $cond, $bind);
    }
}
