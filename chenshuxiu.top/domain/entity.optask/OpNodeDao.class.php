<?php
/*
 * OpNodeDao
 */
class OpNodeDao extends Dao
{
    public static function getByCodeOpTaskTplId ($code, $optasktplid) {
        $cond = " and code = :code and optasktplid = :optasktplid ";
        $bind = [
            ':code' => $code,
            ':optasktplid' => $optasktplid
        ];

        return Dao::getEntityByCond('OpNode', $cond, $bind);
    }

    public static function getListByOpTaskTpl (OpTaskTpl $optasktpl) {
        $cond = " and optasktplid = :optasktplid ";
        $bind = [
            ':optasktplid' => $optasktpl->id
        ];

        return Dao::getEntityListByCond('OpNode', $cond, $bind);
    }

    public static function getFinish(OpTaskTpl $optasktpl) {
        $cond = " AND optasktplid = :optasktplid AND code = 'finish' ";
        $bind = [
            ':optasktplid' => $optasktpl->id
        ];

        return Dao::getEntitByCond('OpNode', $cond, $bind);
    }
}
