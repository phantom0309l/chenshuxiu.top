<?php
// CheckupPictureDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class CheckupPictureDao extends Dao
{
    // 名称: getCntByPatientidNotOpen
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatientidNotOpen ($patientid) {
        $sql = 'select count(*)
            from checkuppictures
            where patientid = :patientid and status = 0
            order by check_date desc, createtime desc ';
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByCheckupid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCheckupid ($checkupid, $patientid = 0, $fromdate = null, $todate = null) {
        $cond = '';
        $bind = [];

        if ($fromdate != null) {
            $cond .= ' and check_date > :fromdate ';
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate != null) {
            $cond .= ' and check_date <= :todate ';
            $bind[':todate'] = $todate;
        }

        if ($patientid > 0) {
            $cond .= ' and patientid = :patientid ';
            $bind[':patientid'] = $patientid;
        }

        $cond .= '  and checkupid = :checkupid order by check_date desc, createtime desc ';

        $bind[':checkupid'] = $checkupid;

        return Dao::getEntityListByCond('CheckupPicture', $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid) {
        $cond = 'and patientid = :patientid order by check_date desc, createtime desc ';
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond('CheckupPicture', $cond, $bind);
    }

}