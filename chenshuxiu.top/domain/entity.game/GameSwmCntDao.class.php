<?php
/*
 * GameSwmCntDao
 */
class GameSwmCntDao extends Dao
{
    // 名称: getByGameplayid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGameplayid ($gameplayid) {
        $bind = [];
        $bind[':gameplayid'] = $gameplayid;

        return Dao::getEntityByCond("GameSwmCnt", "AND gameplayid = :gameplayid ", $bind);
    }
}
