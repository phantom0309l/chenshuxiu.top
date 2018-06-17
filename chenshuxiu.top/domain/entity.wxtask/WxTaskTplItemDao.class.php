<?php
/*
 * WxTaskTplItemDao
 */
class WxTaskTplItemDao extends Dao
{

    // 名称: 任务项模板/任务模板项 列表
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getListBy ($wxtasktplid) {
        $cond = "and wxtasktplid = :wxtasktplid order by pos asc";
        $bind = [];
        $bind[':wxtasktplid'] = $wxtasktplid;

        return Dao::getEntityListByCond("WxTaskTplItem", $cond, $bind);
    }

    // 名称: getOneBy
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getOneBy ($wxtasktplid, $pos) {
        $cond = "AND wxtasktplid = :wxtasktplid AND pos = :pos";
        $bind = [];
        $bind[':wxtasktplid'] = $wxtasktplid;
        $bind[':pos'] = $pos;

        return Dao::getEntityByCond("WxTaskTplItem", $cond, $bind);
    }
}
