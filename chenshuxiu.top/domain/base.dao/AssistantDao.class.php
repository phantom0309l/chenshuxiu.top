<?php
/*
 * AssistantDao
 */
class AssistantDao extends Dao
{
    // 名称: getListAll
    // 备注:
    // 创建:
    // 修改:
    protected static $entityName = 'Assistant';
    public static function getListAll () {
        return Dao::getEntityListByCond("assistants");
    }

    public static function getByUserid($userid) {
        $cond = ' AND userid=:userid ';
        $bind = array (
            ':userid' => $userid,
        );
        return Dao::getEntityByCond(self::$entityName, $cond, $bind);
    }

    public static function getListByDoctorid($doctorid) {
        $cond = ' AND doctorid=:doctorid ';
        $bind = array (
            ':doctorid' => $doctorid,
        );
        return Dao::getEntityListByCond(self::$entityName, $cond, $bind);
    }
}
