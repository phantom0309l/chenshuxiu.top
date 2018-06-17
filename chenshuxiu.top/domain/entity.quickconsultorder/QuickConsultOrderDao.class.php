<?php

/*
 * QuickConsultOrderDao
 */

class QuickConsultOrderDao extends Dao
{
    /**
     * 获取患者最后一条快速咨询
     * @param $patientid
     * @return null
     */
    public static function getLastByPatientid($patientid) {
        $cond = " AND patientid = :patientid ORDER BY id DESC ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('QuickConsultOrder', $cond, $bind);
    }

    /**
     * 列表
     *
     * @param $diseaseids
     * @param string $condEx
     * @return array
     */
    public static function getListByDisease($diseaseids, $condEx = "") {
        $cond = " AND diseaseid IN ({$diseaseids}) {$condEx} ";

        return Dao::getEntityListByCond('QuickConsultOrder', $cond);
    }

    /**
     * 分页列表
     *
     * @param $diseaseids
     * @param $pagesize
     * @param $pagenum
     * @param string $condEx
     * @return array
     */
    public static function getListByDisease4Page($diseaseids, $pagesize, $pagenum, $condEx = "") {
        $cond = " AND diseaseid IN ({$diseaseids}) {$condEx} ";

        return Dao::getEntityListByCond4Page('QuickConsultOrder', $pagesize, $pagenum, $cond);
    }

    /**
     * 获取待处理列表
     * @param $diseaseids
     * @return null
     */
    public static function getPendingListByDisease($diseaseids) {
        $cond = " AND diseaseid IN ({$diseaseids}) AND status = 3 AND is_pay = 1 ORDER BY time_pay ASC ";

        return Dao::getEntityListByCond('QuickConsultOrder', $cond);
    }

    /**
     * 获取待处理数量
     * @param $diseaseids
     * @return null
     */
    public static function getPendingCountByDiseaseids($diseaseids) {
        $sql = "SELECT COUNT(*)
                FROM quickconsultorders
                WHERE diseaseid IN ({$diseaseids}) AND status = 3 AND is_pay = 1 ";

        return Dao::queryValue($sql);
    }

    /**
     * 获取支付总金额
     * @param string $condEx
     * @return null
     */
    public static function getTotalAmount(){
        $sql = "SELECT SUM(a.amount)
                FROM quickconsultorders a
                WHERE a.is_pay = 1  AND a.status != 0";

        return Dao::queryValue($sql);
    }

    /**
     * 获取退款总金额
     * @param string $condEx
     * @return null
     */

    public static function getTotalRefundAmount() {
        $sql = "SELECT sum(a.amount)
                FROM quickconsultorders a
                WHERE a.is_refund = 1";

        return Dao::queryValue($sql);
    }

    /**
     * 本月支付总金额
     * @param string $condEx
     * @return null
     */

    public static function getMonthTotalAmount($starttime,$endtime){
        $sql = "SELECT SUM(a.amount)
                FROM quickconsultorders a
                WHERE a.is_pay = 1 AND a.time_pay BETWEEN :starttime AND :endtime ";

        $bind[':starttime']=$starttime;
        $bind[':endtime']=$endtime;

        return Dao::queryValue($sql,$bind);
    }

    /**
     * 今天支付总金额
     * @param string $condEx
     * @return null
     */

    public static function getTodayTotalAmount($starttime,$endtime){
        $sql = "SELECT SUM(a.amount)
                FROM quickconsultorders a
                WHERE a.is_pay = 1 AND a.time_pay BETWEEN :starttime AND :endtime ";

        $bind[':starttime']=$starttime;
        $bind[':endtime']=$endtime;

        return Dao::queryValue($sql,$bind);
    }



}
