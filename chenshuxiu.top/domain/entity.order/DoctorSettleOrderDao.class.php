<?php
// DoctorSettleOrder
// TODO rework 需要补注释, 改成 bind 模式

// owner by lijie
// create by lijie 2016-5-28 下午4:45
// review by sjp 20160629

class DoctorSettleOrderDao extends Dao
{
    // 名称: getArrByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getArrByDoctorid ($doctorid) {
        $sql = "SELECT left(themonth, 4) as year, left(themonth, 7) as month, sum(amount) as sum
        FROM doctorsettleorders
        WHERE doctorid = :doctorid and amount > 0
        GROUP BY month, year DESC
        ORDER BY year DESC , month DESC ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::queryRows($sql, $bind);
    }

    // 名称: getByDoctoridAndDateYm
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridAndDateYm ($doctorid, $themonth) {
        $cond = "";
        $bind = [];
        if ($doctorid) {
            $cond .= " and doctorid=:doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($themonth) {
            $cond .= " and themonth=:themonth ";
            $bind[':themonth'] = $themonth;
        }

        return Dao::getEntityByCond("DoctorSettleOrder", $cond, $bind);
    }

    // 名称: getOneByDoctoridMonth
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByDoctoridMonth ($doctorid, $month) {
        $cond = " and doctorid=:doctorid and left(themonth, 7)=:themonth";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':themonth'] = $month;

        return Dao::getEntityByCond("DoctorSettleOrder", $cond, $bind);
    }

    // 名称: getRptGroupbyDoctorMonth
    // 备注:
    // 创建:
    // 修改:
    public static function getRptGroupbyDoctorMonth () {
        $sql = "SELECT doctorid, left(themonth, 7) as themonth, activecnt as cnt
                FROM doctorsettleorders";

        return Dao::queryRows($sql, []);
    }
}
