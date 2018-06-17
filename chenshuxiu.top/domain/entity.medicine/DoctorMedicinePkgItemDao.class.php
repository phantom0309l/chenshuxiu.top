<?php
// DoctorMedicinePkgItemDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class DoctorMedicinePkgItemDao extends Dao
{
    // 名称: getListByDoctormedicinepkgid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctormedicinepkgid ($doctormedicinepkgid) {
        $cond = ' and doctormedicinepkgid=:doctormedicinepkgid  ';

        $bind = array(
            ':doctormedicinepkgid' => $doctormedicinepkgid);

        return Dao::getEntityListByCond('DoctorMedicinePkgItem', $cond, $bind);
    }
}