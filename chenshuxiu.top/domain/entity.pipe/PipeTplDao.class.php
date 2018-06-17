<?php
/*
 * PipeTplDao
 */
class PipeTplDao extends Dao
{
	// 名称: getOneByObjtypeAndObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByObjtypeAndObjcode ($objtype, $objcode) {
		$bind = [];
        $cond = " AND objtype = :objtype AND objcode = :objcode ";
        $bind[':objtype'] = $objtype;
        $bind[':objcode'] = $objcode;
        return Dao::getEntityByCond('PipeTpl', $cond, $bind);
    }

	// 名称: getListByObjtype
    // 备注:
    // 创建:
    // 修改:
    public static function getListByObjtype ($objtype) {
		$bind = [];
        $cond = " AND objtype = :objtype";
        $bind[':objtype'] = $objtype;
        return Dao::getEntityListByCond('PipeTpl', $cond, $bind);
    }
}
