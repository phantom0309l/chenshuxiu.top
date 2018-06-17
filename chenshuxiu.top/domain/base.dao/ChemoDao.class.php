<?php
/*
 * ChemoDao
 */
class ChemoDao extends Dao
{
    // 名称: getListAll
    // 备注:
    // 创建:
    // 修改:
    public static function getListAll () {
        return Dao::getEntityListByCond("Chemo");
    }
}
