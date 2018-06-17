<?php
/*
 * Rpt_doctor_monthDao
 */
class Rpt_doctor_monthDao extends Dao {
// 名称: getByDoctoridAndDateYm
    // 备注:
    // 创建: by lijie
    // 修改: by lijie
    protected static $_database = 'statdb';
    public static function getByDoctoridAndDateYm ($doctorid, $themonth) {
        $cond = "  ";

        $bind = [];

        if ($doctorid) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($themonth) {
            $cond .= " and left(themonth, 7) = :themonth ";
            $bind[':themonth'] = $themonth;
        }

        return Dao::getEntityByCond("Rpt_doctor_month", $cond, $bind, self::$_database);
    }

    // 名称: getByThedateYm
    // 备注:
    // 创建:
    // 修改:
    public static function getByThedateYm ($thedateYm) {
        $cond = "AND left(themonth, 7) = :themonth ";

        $bind = [];
        $bind[':themonth'] = $thedateYm;

        return Dao::getEntityByCond("Rpt_doctor_month", $cond, $bind, self::$_database);
    }
}
