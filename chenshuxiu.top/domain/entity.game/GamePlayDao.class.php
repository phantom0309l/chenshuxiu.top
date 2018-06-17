<?php

/*
 * GamePlayDao
 */
class GamePlayDao extends Dao
{
    // 名称: getByGameid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameid ($gameid, $wxuserid) {
        $bind = [];
        $bind[':gameid'] = $gameid;
        $bind[':wxuserid'] = $wxuserid;

        return Dao::getEntityListByCond("GamePlay", "AND gameid = :gameid AND wxuserid = :wxuserid order by id desc", $bind);
    }
}
