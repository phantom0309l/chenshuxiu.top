<?php

/*
 * Drip_greenChannelDao
 */

class Drip_greenChannelDao extends Dao
{
    /**
     * 通过patientid获取最后一条绿色通道申请
     */
    public static function getLastOneByPatientid($patientid) {
        $cond = ' AND patientid = :patientid ORDER BY id DESC ';
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('Drip_greenChannel', $cond, $bind);
    }

    public static function getAllList() {
        $cond = ' ORDER BY id DESC ';

        return Dao::getEntityListByCond('Drip_greenChannel', $cond);
    }

    public static function getListByStatus4Page($status = 'all', $pagenum, $pagesize) {
        $cond = '';
        $bind = [];

        if ($status != 'all') {
            $cond .= ' AND status = :status ';
            $bind = [
                ':status' => $status
            ];
        }

        $cond .= 'ORDER BY id DESC';

        return Dao::getEntityListByCond4Page('Drip_greenChannel', $pagesize, $pagenum, $cond, $bind);
    }

    public static function getCountByStatus($status = 'all') {
        $sql = "SELECT count(*)
                FROM drip_greenchannels
                WHERE 1 = 1 ";
        $bind = [];

        if ($status != 'all') {
            $sql .= ' AND status = :status ';
            $bind = [
                ':status' => $status
            ];
        }

        return Dao::queryValue($sql, $bind);
    }

}