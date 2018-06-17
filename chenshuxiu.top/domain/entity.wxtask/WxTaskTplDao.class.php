<?php
/*
 * WxTaskTplDao
 */
class WxTaskTplDao extends Dao
{
    // 名称: getAll
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getAll () {
        return Dao::getEntityListByCond("WxTaskTpl");
    }

    // 名称: getByEname
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getByEname ($ename) {
        $cond = "and ename = :ename order by id desc";
        $bind = [];
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond("WxTaskTpl", $cond, $bind);
    }
}
