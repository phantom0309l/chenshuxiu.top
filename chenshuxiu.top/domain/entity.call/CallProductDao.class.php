<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/9/27
 * Time: 18:09
 */
/*
 * CallProductDao
 */

class CallProductDao extends Dao
{

    public static function getValidByObjtypeObjid ($objtype, $objid) {
        $cond = " AND objtype=:objtype AND objid=:objid AND status = 1 ";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond('CallProduct', $cond, $bind);
    }

}