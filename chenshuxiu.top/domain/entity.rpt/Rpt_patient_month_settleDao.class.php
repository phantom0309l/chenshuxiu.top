<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-5-28
 * Time: 下午4:53
 */
class Rpt_patient_month_settleDao extends Dao
{
    // 名称: getAvtivecntByDoctorid
    // 备注:
    // 创建: by lijie
    // 修改: by lijie
    protected static $_database = 'statdb';
    public static function getAvtivecntByDoctorid ($doctorid, $year_month) {
        $sql = " SELECT count(id)
            FROM rpt_patient_month_settles
            WHERE doctorid = :doctorid AND LEFT(themonth,7) = :year_month
                AND pipecntbypatient > 0 AND isscan = 1
                AND month_pos > 0 AND month_pos < 7
                AND (patientdaycnt > 0 OR patientdaycnt = '') ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':year_month'] = $year_month;

        return Dao::queryValue($sql, $bind, self::$_database);
    }

    // 名称: getBaodaoCntByDoctorid
    // 备注: TODO lijie 加个注释
    // 创建: by lijie
    // 修改: by lijie
    public static function getBaodaoCntByDoctorid ($doctorid, $year_month) {
        $sql = "SELECT count(id) AS cnt
            FROM rpt_patient_month_settles
            WHERE doctorid = :doctorid
                AND LEFT(themonth,7) = :year_month AND LEFT(baodaodate,7) = :year_month
                AND isscan = 1
                AND (patientdaycnt > 0 OR patientdaycnt = '') ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':year_month'] = $year_month;

        return Dao::queryValue($sql, $bind, self::$_database);
    }

    // 名称: getByPatientidAndDateYmd
    // 备注:
    // 创建: by lijie
    // 修改: by lijie
    public static function getByPatientidAndDateYmd ($patientid, $themonth) {
        $cond = " AND isscan = 1 AND (patientdaycnt > 0 OR patientdaycnt = '') ";

        $bind = [];

        if ($patientid) {
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        if ($themonth) {
            $cond .= " and themonth = :themonth ";
            $bind[':themonth'] = $themonth;
        }

        return Dao::getEntityByCond("Rpt_patient_month_settle", $cond, $bind, self::$_database);
    }

    // 名称: getList_baodao
    // 备注:
    // 创建: by lijie
    // 修改: by lijie
    public static function getList_baodao ($doctorid, $themonth) {
        $cond = " AND doctorid = :doctorid
            AND left(themonth, 7) = :themonth AND LEFT(baodaodate,7) = :themonth
            AND pipecntbypatient > 0 AND isscan = 1
            AND (patientdaycnt > 0 OR patientdaycnt = '') ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':themonth'] = $themonth;

        return Dao::getEntityListByCond("Rpt_patient_month_settle", $cond, $bind, self::$_database);
    }

    // 名称: getList_manage
    // 备注: 患者报到起第二个月到第六个月的活跃患者列表
    // 创建: by lijie
    // 修改: by lijie
    public static function getList_manage ($doctorid, $themonth) {
        $cond = " AND doctorid = :doctorid
            AND left(themonth, 7) = :themonth
            AND pipecntbypatient > 0 AND isscan = 1
            AND month_pos > 1 AND month_pos < 7
            AND (patientdaycnt > 0 OR patientdaycnt = '') ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':themonth'] = $themonth;

        return Dao::getEntityListByCond("Rpt_patient_month_settle", $cond, $bind, self::$_database);
    }

    // 名称: getListGroupByDoctoridAndThemonth
    // 备注: 得到按照doctorid,left(themonth, 7)分组的数据
    // 创建: by lijie
    // 修改: by lijie
    public static function getListGroupByDoctoridAndThemonth ($condfix) {
        $sql = " select
            doctorid,
            left(themonth, 7) as themonth,
            count(id) as cnt
            from rpt_patient_month_settles
            where 1=1 {$condfix}
            group by doctorid, left(themonth, 7) ";

        return Dao::queryRows($sql, array(), self::$_database);
    }

}
