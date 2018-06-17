<?php
/*
 * WxPicMsgDao
 */
class WxPicMsgDao extends Dao
{
    // 名称: getListForWxOfPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListForWxOfPatientid ($patientid) {
        $cond = " and patientid = :patientid and source = 'self' order by id ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("WxPicMsg", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid) {
        $cond = " and patientid = :patientid order by id desc";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("WxPicMsg", $cond, $bind);
    }

    // 名称: getListByObj
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObj ($obj) {
        $cond = " and objtype = :objtype and objid = :objid order by id asc ";

        $bind = [];
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityListByCond("WxPicMsg", $cond, $bind);
    }

    public static function getByPicture (Picture $picture) {
        $cond = " AND pictureid = :pictureid ORDER BY id ASC ";

        $bind = [];
        $bind[':pictureid'] = $picture->id;

        return Dao::getEntityByCond("WxPicMsg", $cond, $bind);
    }

}
