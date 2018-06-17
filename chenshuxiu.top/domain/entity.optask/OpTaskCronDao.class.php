<?php
/*
 * OpTaskCronDao
 */
class OpTaskCronDao extends Dao {
    public static function getListByOptaskidStatus ($optaskid, $status) {
        $cond = " and optaskid = :optaskid and status = :status ";
        $bind = [
            ':optaskid' => $optaskid,
            ':status' => $status
        ];

        return Dao::getEntityListByCond('OpTaskCron', $cond, $bind);
    }

    public static function getByOptaskidOptasktplcronid ($optaskid, $optasktplcronid) {
        $cond = " and optaskid = :optaskid and optasktplcronid = :optasktplcronid ";
        $bind = [
            ':optaskid' => $optaskid,
            ':optasktplcronid' => $optasktplcronid
        ];

        return Dao::getEntityByCond('OpTaskCron', $cond, $bind);
    }
}