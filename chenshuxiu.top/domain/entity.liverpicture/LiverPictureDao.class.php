<?php
/*
 * LiverPictureDao
 */
class LiverPictureDao extends Dao
{
    // 名称: getListByObj
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObj ($obj) {
        $cond = " AND objtype = :objtype AND objid = :objid ORDER BY id ASC ";

        $bind = [];
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityListByCond("LiverPicture", $cond, $bind);
    }

    public static function getByPicture (Picture $picture) {
        $cond = " AND pictureid = :pictureid ORDER BY id ASC ";

        $bind = [];
        $bind[':pictureid'] = $picture->id;

        return Dao::getEntityByCond("LiverPicture", $cond, $bind);
    }

    public static function getListByPicture (Picture $picture) {
        $cond = " AND pictureid = :pictureid ORDER BY id ASC ";

        $bind = [];
        $bind[':pictureid'] = $picture->id;

        return Dao::getEntityListByCond("LiverPicture", $cond, $bind);
    }
}
