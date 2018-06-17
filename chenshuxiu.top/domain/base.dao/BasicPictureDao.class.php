<?php
/*
 * BasicPictureDao
 */
class BasicPictureDao extends Dao
{
    public static function getListByObj($obj) {
        $cond = " AND objtype = :objtype AND objid = :objid ORDER BY id ASC ";

        $bind = [];
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityListByCond("BasicPicture", $cond, $bind);
    }

    public static function getListByObjtypeObjid($objtype, $objid) {
        $cond = " AND objtype=:objtype AND objid=:objid ";
        $bind = [
            ':objtype' => $objtype,
            ':objid' => $objid,
        ];
        return Dao::getEntityListByCond('BasicPicture', $cond, $bind);
    }

    public static function getListByObjtypeObjidAndType($objtype, $objid, $type) {
        $cond = " AND objtype=:objtype AND objid=:objid AND type=:type ";
        $bind = [
            ':objtype' => $objtype,
            ':objid' => $objid,
            ':type' => $type,
        ];
        return Dao::getEntityListByCond('BasicPicture', $cond, $bind);
    }

    public static function getByPicture (Picture $picture) {
        $cond = " AND pictureid = :pictureid ORDER BY id ASC ";

        $bind = [];
        $bind[':pictureid'] = $picture->id;

        return Dao::getEntityByCond("BasicPicture", $cond, $bind);
    }

}
