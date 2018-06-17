<?php

/*
 * ServiceOrderDao
 */

class ServiceOrderDao extends Dao
{

    /**
     * 分页列表
     *
     * @param $type
     * @param $pagesize
     * @param $pagenum
     * @param string $condEx
     * @return array
     */
    public static function getListByType($type, $pagesize, $pagenum, $condEx = "") {
        $sql = "SELECT a.*
                FROM serviceorders a
                WHERE a.serviceproduct_type = :type
                {$condEx} ";
        $bind = [
            ':type' => $type
        ];

        return Dao::loadEntityList4Page('ServiceOrder', $sql, $pagesize, $pagenum, $bind);
    }

    /**
     * 总金额
     *
     * @param $type
     * @param string $condEx
     * @return mixed
     */
    public static function getTotalAmount($type, $condEx = "") {
        $sql = "SELECT sum(a.amount)
                FROM serviceorders a
                WHERE a.serviceproduct_type = :type
                {$condEx}";
        $bind = [
            ':type' => $type
        ];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 退款总金额
     *
     * @param $type
     * @param string $condEx
     */
    public static function getTotalRefundAmount($type, $condEx = "") {
        $sql = "SELECT sum(a.amount)
                FROM serviceorders a
                WHERE a.serviceproduct_type = :type 
                AND a.refund_amount > 0
                {$condEx}";
        $bind = [
            ':type' => $type
        ];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 获取某个患者已支付订单数
     *
     * @param Patient $patient
     * @return mixed
     */
    public static function getIsPayServiceOrderCntByPatientAndType(Patient $patient, $type) {
        $sql = "SELECT count(a.id)
                FROM serviceorders a
                WHERE a.patientid = :patientid
                AND a.serviceproduct_type = :type
                AND a.is_pay = 1";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::queryValue($sql, $bind);
    }
}