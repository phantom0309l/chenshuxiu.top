<?php

/*
 * QuickPass_ServiceItemDao
 */

class QuickPass_ServiceItemDao extends Dao
{

    /**
     * 获取最后一条有效地记录（过期了也算有效，status仅用于判断是否有效，不和有效期关联）
     *
     * @param $patientid
     * @return null
     */
    public static function getLastValidOneByPatientid($patientid) {
        $cond = " AND patientid = :patientid AND status = 1 ORDER BY endtime DESC ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('QuickPass_ServiceItem', $cond, $bind);
    }

    /**
     * 获取该订单最后一条有效记录
     *
     * @param $serviceorderid
     * @return null
     */
    public static function getLastValidOneByServiceOrderid($serviceorderid) {
        $cond = " AND serviceorderid = :serviceorderid AND status = 1 ORDER BY endtime DESC ";
        $bind = [
            ':serviceorderid' => $serviceorderid
        ];

        return Dao::getEntityByCond('QuickPass_ServiceItem', $cond, $bind);
    }

    /**
     * 获取订单明细
     *
     * @param $serviceorderid
     * @return null
     */
    public static function getListByServiceOrderid($serviceorderid) {
        $cond = " AND serviceorderid = :serviceorderid ORDER BY starttime ASC ";
        $bind = [
            ':serviceorderid' => $serviceorderid
        ];

        return Dao::getEntityListByCond('QuickPass_ServiceItem', $cond, $bind);
    }

    /**
     * 获取一条有效地且在时间范围内的记录
     *
     * @param $patientid
     * @param $time
     * @return null
     */
    public static function getValidOneByPatientAndTime($patientid, $time) {
        $cond = " AND patientid = :patientid AND starttime <= :starttime AND endtime >= :endtime AND status = 1 ";
        $bind = [
            ':patientid' => $patientid,
            ':starttime' => $time,
            ':endtime' => $time
        ];

        return Dao::getEntityByCond('QuickPass_ServiceItem', $cond, $bind);
    }

    /**
     * 预退款列表
     *
     * @param string $condEx
     * @return array
     */
    public static function getPreRefundList($condEx = "") {
        $cond = " AND is_refund = 1 AND time_refund = '0000-00-00 00:00:00' {$condEx} ";
        $bind = [];

        return Dao::getEntityListByCond('QuickPass_ServiceItem', $cond, $bind);
    }
}