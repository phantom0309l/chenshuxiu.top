<?php

/*
 * GameCombatSwmDao
 */
class GameCombatSwmDao extends Dao
{
    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatSwm", "AND gameplayid = :gameplayid ", $bind);
    }

    // 名称: getSum
    // 备注:
    // 创建:
    // 修改:
    public static function getSum ($gameplayid, $item) {
        $sql = "select sum({$item}) from gamecombatswms where gameplayid = :gameplayid ";

        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::queryValue($sql, $bind);
    }
}
