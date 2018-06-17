<?php

/**
 * XMapper
 * @desc		活动记录映射器
 * @remark		依赖类: XEntity
 * @copyright	(c)2012 xwork.
 * @file		XMapper.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class XMapper extends DaoBase
{
    // /////////////////////////////////////////////////////////////
    // 静态方法 sql 语句生成方法

    // 生成update命令
    public static function getUpdateCommand (XEntity $entity) {
        $dirtyKeys = $entity->getDirtyKeys();
        if (empty($dirtyKeys)) {
            return array();
        }

        $tableName = self::getTableNameImp($entity->getClassName(), $entity->tableno, $entity->getDatabaseName());
        $id = $entity->id;
        $row = $entity->toArray();

        foreach ($dirtyKeys as $column) {
            $value = $row[$column];

            $updateParty[] = "`{$column}`=:{$column}";
            $bindValues[":" . $column] = $value;
        }
        $updateStr = implode(",", $updateParty);

        $pkeyName = $entity->getPkeyname();

        $sqls[] = array(
            'sql' => "update $tableName set $updateStr where `{$pkeyName}`={$id} ",
            'param' => $bindValues);

        // 修补,如果不能保证肯定能更新数据请不要覆写此函数
        $sqlsFix = $entity->getUpdateSqlsFix();

        // 返回并集
        return array_merge($sqls, $sqlsFix);
    }

    // 取得删除实体的sql语句，包括删除的修正sql语句
    public static function getDeleteCommand (XEntity $entity) {
        $tableName = self::getTableNameImp($entity->getClassName(), $entity->tableno, $entity->getDatabaseName());
        $id = $entity->id;

        $pkeyName = $entity->getPkeyname();
        $sqls[] = array(
            'sql' => "delete from $tableName where `{$pkeyName}`={$id}",
            'param' => array());

        // 修补,如果不能保证肯定能更新数据请不要覆写此函数
        $sqlsFix = $entity->getDeleteSqlsFix();

        // 返回并集,先执行fix
        return array_merge($sqlsFix, $sqls);
    }

    // /////////////////////////////////////////////////////////////
    // 通用方法-获取实体或实体数组
    // 替代各实体里面的 getById,返回值为实体
    public static function getEntityById ($entityClassName, $id, $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->getById($id);
    }

    // 通用实体查询方法,返回值为实体
    public static function getEntityByCond ($entityClassName, $cond = "", $bind = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->getByCond($cond, $bind);
    }

    // 通用实体列表查询方法,返回值为实体数组
    public static function getEntityListByCond ($entityClassName, $cond = "", $bind = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->getArrayByCond($cond, $bind);
    }

    // 根据ids获取数据
    public static function getEntityListByIds ($entityClassName, $ids = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->getArrayByIds($ids);
    }

    // 通用实体分页查询方法,返回值为实体数组
    public static function getEntityListByCond4Page ($entityClassName, $pagesize, $pagenum, $cond = "", $bind = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->getArrayByCond4Page($cond, $pagesize, $pagenum, $bind);
    }

    // 直接sql，加载实体，(select * from ....) or (select a.* from ....)
    public static function loadEntity ($entityClassName, $sql, $bind = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->load($sql, $bind);
    }

    // 直接sql，加载实体列表，(select * from ....) or (select a.* from ....)
    public static function loadEntityList ($entityClassName, $sql, $bind = array(), $dbconf = array()) {
        $mapper = new XMapper($entityClassName, $dbconf);
        return $mapper->loadArray($sql, $bind);
    }
}