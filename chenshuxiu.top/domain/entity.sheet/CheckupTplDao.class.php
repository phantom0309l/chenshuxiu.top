<?php
// CheckupTplDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class CheckupTplDao extends Dao
{
    // 名称: getCntByDoctorIdAndDiseaseId
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByDoctorIdAndDiseaseId($doctorid, $diseaseid) {
        $sql = ' SELECT COUNT(*)
            FROM checkuptpls
            WHERE doctorid = :doctorid AND diseaseid = :diseaseid ';

        $bind = array(
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByDoctorAndDiseaseid_isInTkt_isInAdmin
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorAndDiseaseid_isInTkt_isInAdmin(Doctor $doctor, $diseaseid = null, $is_in_tkt = null, $is_in_admin = null) {
        $cond = ' AND doctorid = :doctorid'; 

        $bind = [
            ':doctorid' => $doctor->id,
        ];

        if ($diseaseid != null) {
            $cond .= ' AND diseaseid = :diseaseid ';
            $bind[':diseaseid'] = $diseaseid;
        }

        if ($is_in_tkt != null) {
            $cond .= ' AND is_in_tkt = :is_in_tkt ';
            $bind[':is_in_tkt'] = $is_in_tkt;
        }

        if ($is_in_admin != null) {
            $cond .= ' AND is_in_admin = :is_in_admin ';
            $bind[':is_in_admin'] = $is_in_admin;
        }

        $cond .= ' ORDER BY pos ASC ';

        return Dao::getEntityListByCond('CheckupTpl', $cond, $bind);
    }

    // 名称: getByDoctorEname
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctorEname(Doctor $doctor, $ename) {
        $cond = ' and doctorid = :doctorid and ename = :ename ';

        $bind = array(
            ':doctorid' => $doctor->id,
            ':ename' => $ename);

        return Dao::getEntityByCond('CheckupTpl', $cond, $bind);
    }

}
