<?php
/*
 * GameDao
 */
class GameDao extends Dao
{
    // 名称: getByEname
    // 备注:
    // 创建:
    // 修改:
    public static function getByEname ($ename) {
        $bind = array(
            ':ename' => $ename);
        return Dao::getEntityByBind('Game', $bind);
    }
}