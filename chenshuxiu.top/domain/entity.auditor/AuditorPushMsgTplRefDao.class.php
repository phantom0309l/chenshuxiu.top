<?php
/*
 * AuditorPushMsgTplRefDao
 */
class AuditorPushMsgTplRefDao extends Dao {

	public static function getByAuditoridAndPushMsgTplid ($auditorid, $auditorpushmsgtplid, $condFix = "") {
		$cond = " and auditorid = :auditorid and auditorpushmsgtplid = :auditorpushmsgtplid " . $condFix;

        $bind = [];
        $bind[':auditorid'] = $auditorid;
		$bind[':auditorpushmsgtplid'] = $auditorpushmsgtplid;

        return Dao::getEntityByCond('AuditorPushMsgTplRef', $cond, $bind);
    }

	public static function getListByAuditorPushMsgTplIdAndCan_ops ($auditorpushmsgtplid, $can_ops) {
		$cond = " and auditorpushmsgtplid = :auditorpushmsgtplid and can_ops = :can_ops ";

		$bind = [];
		$bind[':auditorpushmsgtplid'] = $auditorpushmsgtplid;
		$bind[':can_ops'] = $can_ops;

		return Dao::getEntityListByCond('AuditorPushMsgTplRef', $cond, $bind);
    }
}
