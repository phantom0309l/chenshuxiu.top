<?php

/*
 * GameCombatSocDao
 */
class GameCombatSocDao extends Dao
{
    // 名称: getAvgNum
    // 备注:
    // 创建:
    // 修改:
    public static function getAvgNum ($gameplayid) {
        $avg = 0;
        $blcs = self::getByGameplayid($gameplayid);
        $cnt = count($blcs);
        $sumcnt = 0 + self::getSumCnt($gameplayid);
        if ($cnt > 0) {
            $avg = $sumcnt / $cnt;
        }

        return $avg;
    }

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

        return Dao::getEntityListByCond("GameCombatSoc", "AND gameplayid = :gameplayid ", $bind);
    }

    // 名称: getSumCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getSumCnt ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::queryValue("select sum(num) from gamecombatsocs where gameplayid = :gameplayid ", $bind);
    }
}
