<?php
// DoctorMedicinePkgDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class DoctorMedicinePkgDao extends Dao
{
    // 名称: getListBydoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListBydoctorid ($doctorid,$diseaseid = 0) {
        $cond = ' and doctorid=:doctorid  ';

        $bind = array(
            ':doctorid' => $doctorid);

        if( $diseaseid ){
            $cond = ' and diseaseid=:diseaseid  ';
            $bind[':diseaseid'] = $diseaseid;
        }

        return Dao::getEntityListByCond('DoctorMedicinePkg', $cond, $bind);
    }
}
