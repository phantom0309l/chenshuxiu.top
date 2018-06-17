<?php
/*
 * WxTaskItemDao
 */
class WxTaskItemDao extends Dao
{
    // 名称: getCurrItem
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getCurrItem ($wxtaskid, $starttime) {
        $cond = "AND wxtaskid = :wxtaskid AND starttime = :starttime order by id asc";
        $bind = [];
        $bind[':wxtaskid'] = $wxtaskid;
        $bind[':starttime'] = $starttime;

        return Dao::getEntityByCond("WxTaskItem", $cond, $bind);
    }

    // 名称: getLastSigned
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getLastSigned ($wxtaskid) {
        $cond = "AND wxtaskid = :wxtaskid AND status = 1 order by starttime desc";
        $bind = [];
        $bind[':wxtaskid'] = $wxtaskid;

        return Dao::getEntityByCond("WxTaskItem", $cond, $bind);
    }

    // 名称: 任务项列表
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getListByWxtaskid ($wxtaskid) {
        $cond = "AND wxtaskid = :wxtaskid order by id asc";
        $bind = [];
        $bind[':wxtaskid'] = $wxtaskid;

        return Dao::getEntityListByCond("WxTaskItem", $cond, $bind);
    }

}
