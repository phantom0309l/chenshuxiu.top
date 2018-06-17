<?php

/*
 * GameSwmpCntDao
 */
class GameSwmpCntDao extends Dao
{
    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityByCond("GameSwmpCnt", "AND gameplayid = :gameplayid ", $bind);
    }
}
