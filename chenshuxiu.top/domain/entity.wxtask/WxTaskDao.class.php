<?php
/*
 * WxTaskDao
 */
class WxTaskDao extends Dao
{
    // 名称: 获取总任务数
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getAllCnt () {
        $sql = "select count(*) from wxtasks";
        return Dao::queryValue($sql, []);
    }

    // 名称: 获取某wxuser的最后一个ename任务
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getLastByEname ($wxuserid, $ename) {
        $cond = "AND wxuserid = :wxuserid AND ename = :ename order by id desc limit 1 ";

        $bind = [];
        $bind[':wxuserid'] = $wxuserid;
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond("WxTask", $cond, $bind);
    }

    // 名称: getListByEname
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getListByEname ($ename, $limit = '') {
        $str = "";
        if ($limit) {
            $limit = intval($limit);
            $str = "limit {$limit}";
        }
        $cond = "AND ename = :ename order by id desc {$str}";

        $bind = [];
        $bind[':ename'] = $ename;

        return Dao::getEntityListByCond("WxTask", $cond, $bind);
    }

    // 名称: 获取任务列表 By WxuseridEname
    // 备注: 无
    // 创建: by txj
    // 修改: by txj
    public static function getListByWxuseridEname ($wxuserid, $ename) {
        $cond = "AND wxuserid = :wxuserid AND ename = :ename order by id desc";

        $bind = [];
        $bind[':wxuserid'] = $wxuserid;
        $bind[':ename'] = $ename;

        return Dao::getEntityListByCond("WxTask", $cond, $bind);
    }
}
