<?php

/*
 * ErrandOrderDao
 */

class ErrandOrderDao extends Dao
{
    /**
     * 获取患者最后一条有效订单
     * @param $patientid
     * @return null
     */
    public static function getLastValidOneByPatientid($patientid) {
        $cond = " AND patientid = :patientid AND status = 1 ORDER BY id DESC ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('ErrandOrder', $cond, $bind);
    }

    /**
     * 获取某个患者已支付订单数
     *
     * @param Patient $patient
     * @return int
     */
    public static function getIsPayErrandOrderCntByPatient(Patient $patient) {
        $sql = "SELECT count(id)
                FROM errandorders
                WHERE patientid = :patientid
                AND is_pay = 1";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::queryValue($sql, $bind) ?? 0;
    }

    /**
     * 获取列表
     *
     * @param $pagesize
     * @param $pagenum
     * @param $condEx
     * @return array
     */
    public static function getListByCondEx($pagesize, $pagenum, $condEx) {
        return Dao::getEntityListByCond4Page('ErrandOrder', $pagesize, $pagenum, $condEx);
    }

    /**
     * 获取数量
     *
     * @param $condEx
     * @return mixed
     */
    public static function getCountByCondEx($condEx) {
        $sql = "SELECT count(id)
                FROM errandorders
                WHERE 1 = 1{$condEx} ";
        return Dao::queryValue($sql);
    }

    /**
     * 总金额
     *
     * @param string $condEx
     * @return mixed
     */
    public static function getTotalAmount($condEx = "") {
        $sql = "SELECT sum(amount)
                FROM errandorders
                WHERE 1 = 1 {$condEx}";
        $bind = [];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 退款总金额
     *
     * @param string $condEx
     */
    public static function getTotalRefundAmount($condEx = "") {
        $sql = "SELECT sum(amount)
                FROM errandorders
                WHERE refund_amount > 0
                {$condEx}";
        $bind = [];

        return Dao::queryValue($sql, $bind);
    }
}