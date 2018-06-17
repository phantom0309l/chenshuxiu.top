<?php
/*
 * OptLogDao
 */
class OptLogDao extends Dao
{
    // 名称: getListByOptaskid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByOptaskid ($optaskid) {
        $cond = "AND optaskid = :optaskid order by id desc";
        $bind = array(
            ":optaskid" => $optaskid);
        return Dao::getEntityListByCond("OptLog", $cond, $bind);
    }

}
