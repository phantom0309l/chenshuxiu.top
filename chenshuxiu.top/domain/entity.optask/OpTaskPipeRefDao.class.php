<?php
/*
 * OpTaskPipeRefDao
 */
class OpTaskPipeRefDao extends Dao {
    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($condEx = "") {
        $cond = " {$condEx}";
        return Dao::getEntityListByCond("OpTaskPipeRef", $cond);
    }

    // 名称: getListByOptaskid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByOptaskid ($optaskid, $condEx="") {
        $cond = " and optaskid = :optaskid {$condEx}";
        $bind = [];
        $bind[":optaskid"] = $optaskid;
        return Dao::getEntityListByCond("OpTaskPipeRef", $cond, $bind);
    }

    // 名称: getOneByOptaskidPipeid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByOptaskidPipeid ($optaskid, $pipeid) {
        $cond = " and optaskid = :optaskid and pipeid = :pipeid";
        $bind = [];
        $bind[":optaskid"] = $optaskid;
        $bind[":pipeid"] = $pipeid;
        return Dao::getEntityByCond("OpTaskPipeRef", $cond, $bind);
    }

}
