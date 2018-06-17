<?php
/*
 * Rpt_patient_monthDao
 */
class Rpt_patient_monthDao extends Dao {
// 名称: getByPatientidAndDateYmd
    // 备注:
    // 创建: by lijie
    // 修改: by lijie
    protected static $_database = 'statdb';
    public static function getByPatientidAndDateYmd ($patientid, $themonth) {
        $cond = " ";

        $bind = [];

        if ($patientid) {
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        if ($themonth) {
            $cond .= " and themonth = :themonth ";
            $bind[':themonth'] = $themonth;
        }

        return Dao::getEntityByCond("Rpt_patient_month", $cond, $bind, self::$_database);
    }

    // 名称: getByThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getByThedateYm ($thedateYm) {
        $fromdate = date("Y-m-d", strtotime($thedateYm));
        $todate = date("Y-m-d", strtotime("next month", strtotime($thedateYm)));
        $cond = "AND themonth >= :fromdate AND themonth < :todate ";

        $bind = [];
        $bind[':fromdate'] = $fromdate;
        $bind[':todate'] = $todate;

        return Dao::getEntityByCond("Rpt_patient_month", $cond, $bind, self::$_database);
    }
}
