<?php

/*
 * GameCombatGngDao
 */
class GameCombatGngDao extends Dao
{
    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatGng", "AND gameplayid = :gameplayid ", $bind);
    }

    // 名称: getIsMiss
    // 备注:
    // 创建:
    // 修改:
    public static function getIsMiss ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatGng", "AND gameplayid = :gameplayid AND clicktype = 0", $bind);
    }

    // 名称: getIsRight
    // 备注:
    // 创建:
    // 修改:
    public static function getIsRight ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatGng", "AND gameplayid = :gameplayid AND clicktype = 1", $bind);
    }

    // 名称: getIsError
    // 备注:
    // 创建:
    // 修改:
    public static function getIsError ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatGng", "AND gameplayid = :gameplayid AND clicktype = 2", $bind);
    }

    // 名称: getRightAvgTime
    // 备注:
    // 创建:
    // 修改:
    public static function getRightAvgTime ($gameplayid) {
        $avg = 0;
        $ms = 0;
        $blcs = self::getIsRight($gameplayid);
        $cnt = count($blcs);
        if ($cnt > 0) {
            foreach ($blcs as $a) {
                $ms += $a->dif;
            }
            $avg = $ms / $cnt;
        }
        return $avg;
    }
}
