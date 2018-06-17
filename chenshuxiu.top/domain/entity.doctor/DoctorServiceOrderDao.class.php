<?php
/*
 * DoctorServiceOrderDao
 */
class DoctorServiceOrderDao extends Dao {
    public static function getAmountSumByDoctorThe_month (Doctor $doctor, $the_month) {
        $sql = "select sum(amount)
                from doctorserviceorders
                where doctorid = :doctorid and the_month = :the_month";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':the_month'] = $the_month;

        return 0 + Dao::queryValue($sql, $bind);
    }

    public static function getAmountSumOfNeedRechargeByDoctorThe_month (Doctor $doctor, $the_month) {
        $sql = "select sum(amount)
                from doctorserviceorders
                where doctorid = :doctorid and the_month = :the_month and is_recharge = 0";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':the_month'] = $the_month;

        return 0 + Dao::queryValue($sql, $bind);
    }

    public static function getListByDoctorFrom_dateEnd_date(Doctor $doctor, $from_date, $end_date){
        $cond = " and doctorid = :doctorid and from_date >= :from_date and end_date <= :end_date order by id asc";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':from_date'] = $from_date;
        $bind[':end_date'] = $end_date;

        return Dao::getEntityListByCond('DoctorServiceOrder', $cond, $bind);
    }

    public static function getListOfNeedRechargeByDoctorThe_month(Doctor $doctor, $the_month){
        $cond = " and doctorid = :doctorid and the_month = :the_month and is_recharge = 0 order by id asc";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':the_month'] = $the_month;

        return Dao::getEntityListByCond('DoctorServiceOrder', $cond, $bind);
    }

    public static function getListOfNeedRechargeByDoctorFrom_dateEnd_date(Doctor $doctor, $from_date, $end_date){
        $cond = " and doctorid = :doctorid and from_date >= :from_date and end_date <= :end_date and is_recharge = 0 order by id asc";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':from_date'] = $from_date;
        $bind[':end_date'] = $end_date;

        return Dao::getEntityListByCond('DoctorServiceOrder', $cond, $bind);
    }

    public static function getListByDoctorObjcodeFrom_dateEnd_date(Doctor $doctor, $objcode, $from_date, $end_date){
        $cond = " and doctorid = :doctorid and objcode = :objcode and from_date >= :from_date and end_date <= :end_date order by id asc";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':objcode'] = $objcode;
        $bind[':from_date'] = $from_date;
        $bind[':end_date'] = $end_date;

        return Dao::getEntityListByCond('DoctorServiceOrder', $cond, $bind);
    }

    public static function getListByDoctorWeek_from_begin(Doctor $doctor, $week_from_begin){
        $cond = " and doctorid = :doctorid and week_from_begin = :week_from_begin";
        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $bind[':week_from_begin'] = $week_from_begin;

        return Dao::getEntityListByCond('DoctorServiceOrder', $cond, $bind);
    }

}
