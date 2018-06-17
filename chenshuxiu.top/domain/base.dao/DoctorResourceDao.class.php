<?php
/*
 * DoctorResourceDao
 */
class DoctorResourceDao extends Dao
{
    // 名称: getListAll
    // 备注:
    // 创建:
    // 修改:
    protected static $entityName = 'DoctorResource';
    public static function getListAll () {
        return Dao::getEntityListByCond("doctorresources");
    }

    public static function getByName($name) {
        $cond = ' AND name=:name ';
        $bind = array (
            ':name' => $name,
        );

        return Dao::getEntityByCond(self::$entityName, $cond, $bind);
    }

    public static function getByActionMethod($action, $method) {
        $cond = ' AND action=:action AND method=:method';
        $bind = array (
            ':action' => $action,
            ':method' => $method,
        );

        return Dao::getEntityByCond(self::$entityName, $cond, $bind);
    }
}
