<?php
// FitPageDao

// owner by fhw
// create by fhw
// review by sjp 20160628
class FitPageDao extends Dao
{
    // 名称: getByCodeDiseaseidDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getByCodeDiseaseidDoctorid ($code, $diseaseid, $doctorid = 0) {
        $cond = ' and code=:code and diseaseid=:diseaseid and doctorid=:doctorid ';
        $bind = array(
            ':code' => $code,
            ':diseaseid' => $diseaseid,
            ':doctorid' => $doctorid);

        return Dao::getEntityByCond('FitPage', $cond, $bind);
    }

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList () {
        $cond = " ORDER BY id ASC ";
        return Dao::getEntityListByCond("FitPage", $cond, []);
    }

    // 名称: getListByFitPageTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getListByFitPageTpl (FitPageTpl $fitpagetpl) {
        $cond = " and fitpagetplid = :fitpagetplid ";
        $bind = [];
        $bind[':fitpagetplid'] = $fitpagetpl->id;

        return Dao::getEntityListByCond('FitPage', $cond, $bind);
    }
}