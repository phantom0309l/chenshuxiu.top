<?php
/*
 * PipeLevelDao
 */
class PipeLevelDao extends Dao {
	public static function getByPipeidAndAuditorid($pipeid, $auditorid) {
		$cond = " and pipeid = :pipeid and auditorid = :auditorid ";
		$bind = [];
		$bind[':pipeid'] = $pipeid;
		$bind[':auditorid'] = $auditorid;

		return Dao::getEntityByCond('PipeLevel', $cond, $bind);
	}

	public static function getHasHandledByPipeid($pipeid) {
		$cond = " and pipeid = :pipeid and is_urgent > 0 ";
		$bind = [];
		$bind[':pipeid'] = $pipeid;

		return Dao::getEntityByCond('PipeLevel', $cond, $bind);
	}

}
