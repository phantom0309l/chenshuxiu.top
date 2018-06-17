<?php

/*
 * GameCombatSwmpDao
 */
class GameCombatSwmpDao extends Dao
{
    // 名称: getAvgTime
    // 备注:
    // 创建:
    // 修改:
    public static function getAvgTime ($gameplayid) {
        $avg = 0;
        $ms = 0;
        $blcs = self::getByGameplayid($gameplayid);
        $cnt = count($blcs);
        if ($cnt > 0) {
            foreach ($blcs as $a) {
                $ms += $a->doms - $a->startms;
            }
            $avg = $ms / $cnt;
        }

        return $avg;
    }

    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatSwmp", "AND gameplayid = :gameplayid ", $bind);
    }

    // 名称: getIsRight
    // 备注:
    // 创建:
    // 修改:
    public static function getIsRight ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityListByCond("GameCombatSwmp", "AND gameplayid = :gameplayid AND isright = 1", $bind);
    }
}
