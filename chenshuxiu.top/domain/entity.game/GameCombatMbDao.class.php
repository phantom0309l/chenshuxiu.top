<?php

/*
 * GameCombatMbDao
 */
class GameCombatMbDao extends Dao
{
    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatMb", "AND gameplayid = :gameplayid ", $bind);
    }

    // 名称: getIsRight
    // 备注:
    // 创建:
    // 修改:
    public static function getIsRight ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatMb", "AND gameplayid = :gameplayid AND isright = 1", $bind);
    }

    // 名称: getScores
    // 备注:
    // 创建:
    // 修改:
    public static function getScores ($gameplayid) {
        $scores = 0;
        $blcs = self::getByGameplayid($gameplayid);
        foreach ($blcs as $a) {
            $scores += $a->score;
        }

        return $scores;
    }
}
