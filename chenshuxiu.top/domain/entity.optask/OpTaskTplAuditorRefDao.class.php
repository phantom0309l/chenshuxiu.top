<?php

/*
 * OpTaskTplAuditorRefDao
 */
class OpTaskTplAuditorRefDao extends Dao
{

    // 名称: getOneByOptasktplidAuditorid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByOptasktplidAuditorid ($optasktplid, $auditorid) {
        $cond = " and optasktplid = :optasktplid and auditorid = :auditorid";
        $bind = [];
        $bind[":optasktplid"] = $optasktplid;
        $bind[":auditorid"] = $auditorid;
        return Dao::getEntityByCond("OpTaskTplAuditorRef", $cond, $bind);
    }
}
