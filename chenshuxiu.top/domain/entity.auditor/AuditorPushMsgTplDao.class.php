<?php
/*
 * AuditorPushMsgTplDao
 */
class AuditorPushMsgTplDao extends Dao {

	public static function getByEname ($ename) {
		$cond = " and ename = :ename ";

        $bind = [];
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond('AuditorPushMsgTpl', $cond, $bind);
    }

}
