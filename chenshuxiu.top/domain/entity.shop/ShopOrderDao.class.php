<?php

/*
 * ShopOrderDao
 */

class ShopOrderDao extends Dao
{

    // 获取订单列表
    public static function getShopOrdersByPatient(Patient $patient) {
        $cond = " and patientid = :patientid";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取某个患者当前订单数
    public static function getShopOrderCntByPatient(Patient $patient) {
        $sql = "select count(*)
                from shoporders
                where patientid = :patientid";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某个患者已支付订单数
    public static function getIsPayShopOrderCntByPatient(Patient $patient) {
        $sql = "select count(*)
                from shoporders
                where patientid = :patientid and is_pay = 1";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某个患者, 某种类型订单（药品、非药品）已支付订单数
    public static function getIsPayShopOrderCntByPatientType(Patient $patient, $type) {
        $sql = "select count(*)
                from shoporders
                where patientid = :patientid and is_pay = 1 and type = :type";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某个患者当天订单数
    public static function getShopOrderCntByPatientTime_paydate(Patient $patient, $time_paydate) {
        $sql = "select count(*)
                from shoporders
                where patientid = :patientid and left(time_pay,10) = :time_paydate";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':time_paydate'] = $time_paydate;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取某天的订单数
    public static function getShopOrderCntByTime_paydate($time_paydate) {
        $sql = "select count(*)
                from shoporders
                where is_pay = 1 and left(time_pay,10) = :time_paydate";

        $bind = [];
        $bind[':time_paydate'] = $time_paydate;

        return Dao::queryValue($sql, $bind) + 0;
    }

    // 获取订单列表
    public static function getShopOrdersByPatientType(Patient $patient, $type) {
        $cond = " and patientid = :patientid and type = :type order by id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前未支付订单
    public static function getNotPayShopOrderByPatient(Patient $patient) {
        $cond = " and patientid = :patientid and is_pay = 0 order by id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前未支付订单
    public static function getNotPayShopOrderByPatientType(Patient $patient, $type) {
        $cond = " and patientid = :patientid and type = :type and is_pay = 0 order by id desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::getEntityByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前已支付订单
    public static function getIsPayShopOrdersByPatient(Patient $patient) {
        $cond = " and patientid = :patientid and is_pay = 1 order by time_pay desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前已支付订单 list
    public static function getIsPayShopOrdersByPatientType(Patient $patient, $type) {
        $cond = " and patientid = :patientid and type = :type and is_pay = 1 order by time_pay desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取医生当前已支付订单 list
    public static function getIsPayShopOrdersByDoctorType(Doctor $doctor, $type) {
        $cond = " and the_doctorid = :the_doctorid and type = :type and is_pay = 1 order by time_pay desc";
        $bind = [];
        $bind[':the_doctorid'] = $doctor->id;
        $bind[':type'] = $type;

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前已支付订单 one
    public static function getIsPayShopOrderByPatient(Patient $patient) {
        $cond = " and patientid = :patientid and is_pay = 1 order by time_pay desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityByCond('ShopOrder', $cond, $bind);
    }

    // 获取当前已支付订单 one
    public static function getIsPayShopOrderByPatientType(Patient $patient, $type) {
        $cond = " and patientid = :patientid and type = :type and is_pay = 1 order by time_pay desc";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':type'] = $type;

        return Dao::getEntityByCond('ShopOrder', $cond, $bind);
    }

    // 获取医生每个月已支付订单数
    public static function getIsPayShopOrderCntArrByDoctor(Doctor $doctor) {
        $sql = "select count(*) as cnt, left(time_pay,7) as themonth
                from shoporders
                where the_doctorid = :the_doctorid and is_pay=1 group by themonth";

        $bind = [];
        $bind[':the_doctorid'] = $doctor->id;

        return Dao::queryRows($sql, $bind);
    }

    //获取医生每个月订单金额（不包含运费）
    public static function getIsPayShopOrderAmountArrByDoctor(Doctor $doctor) {
        $sql = "select sum(item_sum_price-refund_amount) as cnt, left(time_pay,7) as themonth
                from shoporders
                where the_doctorid = :the_doctorid and is_pay=1 group by themonth";

        $bind = [];
        $bind[':the_doctorid'] = $doctor->id;

        return Dao::queryRows($sql, $bind);
    }

    // 获取医生某段时间已支付订单数
    public static function getIsPayShopOrderCntByDoctorStartdateEnddate(Doctor $doctor, $startdate, $enddate) {
        $sql = "select count(*) as cnt
                from shoporders
                where the_doctorid = :the_doctorid and is_pay=1 and time_pay >= :startdate and time_pay < :enddate";

        $bind = [];
        $bind[':the_doctorid'] = $doctor->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryValue($sql, $bind) + 0;
    }

    //获取医生某段时间订单金额（不包含运费）
    public static function getIsPayShopOrderAmountByDoctorStartdateEnddate(Doctor $doctor, $startdate, $enddate) {
        $sql = "select sum(cast(item_sum_price as signed)-cast(refund_amount as signed))
                from shoporders
                where the_doctorid = :the_doctorid and is_pay=1 and time_pay >= :startdate and time_pay < :enddate";

        $bind = [];
        $bind[':the_doctorid'] = $doctor->id;
        $bind[':startdate'] = $startdate;
        $bind[':enddate'] = date("Y-m-d H:i:s", strtotime($enddate) + 86400);

        return Dao::queryValue($sql, $bind) + 0;
    }

    //获取市场某段时间内，其管辖医生的订单数
    public static function getIsPayShopOrderCntByAuditorMarketStartdateEnddate(auditor $auditor_market, $startdate, $enddate) {
        $doctors = DoctorDao::getListByAuditorid_market($auditor_market->id);

        $cnt = 0;
        foreach($doctors as $doctor){
            $cnt += self::getIsPayShopOrderCntByDoctorStartdateEnddate($doctor, $startdate, $enddate);
        }
        return $cnt;
    }

    //获取市场某段时间内，其管辖医生的订单金额数
    public static function getIsPayShopOrderAmountByAuditorMarketStartdateEnddate(auditor $auditor_market, $startdate, $enddate) {
        $doctors = DoctorDao::getListByAuditorid_market($auditor_market->id);

        $amount = 0;
        foreach($doctors as $doctor){
            $amount += self::getIsPayShopOrderAmountByDoctorStartdateEnddate($doctor, $startdate, $enddate);
        }
        return $amount;
    }

    // 获取已支付订单列表 by doctor
    public static function getListByDoctorAndRange(Doctor $doctor, $from, $to) {
        $cond = " AND the_doctorid = :the_doctorid AND is_pay = 1 AND time_pay >= :from AND time_pay < :to ORDER BY time_pay DESC ";
        $bind = [
            ':the_doctorid' => $doctor->id,
            ':from' => $from,
            ':to' => $to,
        ];

        return Dao::getEntityListByCond('ShopOrder', $cond, $bind);
    }

    // 获取医生最新一条已支付订单
    public static function getLatestOneByDoctorAndDate(Doctor $doctor, $to) {
        $cond = " AND the_doctorid = :the_doctorid AND is_pay = 1 AND time_pay < :to ORDER BY time_pay DESC ";
        $bind = [
            ':the_doctorid' => $doctor->id,
            ':to' => $to,
        ];

        return Dao::getEntityByCond('ShopOrder', $cond, $bind);
    }

}
