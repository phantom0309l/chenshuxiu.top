<?php
/*
 * DoctorCommentDao
 */
class DoctorCommentDao extends Dao
{
    public static function getListByObjtypeAndObjid($objtype, $objid) {
        $cond = " AND objtype = :objtype AND objid = :objid ORDER BY id DESC";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];
        return Dao::getEntityListByCond('DoctorComment', $cond, $bind);
    }

    public static function getByObjtypeAndObjid($objtype, $objid) {
        $cond = " AND objtype = :objtype AND objid = :objid";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];
        return Dao::getEntityByCond('DoctorComment', $cond, $bind);
    }

}