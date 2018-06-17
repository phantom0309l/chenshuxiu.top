<?php

/*
 * Guest_faceRecordDao
 */
class Guest_faceRecordDao extends Dao
{
    // 名称: getCntByGuest_faceid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByGuest_faceid ($guest_faceid) {
        $sql = 'select count(*) from guest_facerecords where guest_faceid=:guest_faceid';

        $bind = [];
        $bind[':guest_faceid'] = $guest_faceid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByGuest_faceid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByGuest_faceid ($guest_faceid) {
        $cond = " and guest_faceid = :guest_faceid order by id desc ";

        $bind = [];
        $bind[':guest_faceid'] = $guest_faceid;

        return Dao::getEntityListByCond('Guest_faceRecord', $cond, $bind);
    }
}
