<?php
/*
 * MediaDao
 */
class MediaDao extends Dao
{
    // 名称: getOneByObj3
    // 备注: 三元式
    // 创建: by lijie
    // 修改: by lijie
    public static function getOneByObj3 ($objtype, $objid, $objcode) {
        $cond = "AND objtype = :objtype AND objid = :objid AND objcode = :objcode order by createtime";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;
        $bind[':objcode'] = $objcode;

        return Dao::getEntityByCond("Media", $cond, $bind);
    }
}
