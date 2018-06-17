<?php
/*
 * OpTaskOpNodeLogDao
 */
class OpTaskOpNodeLogDao extends Dao
{
	public static function getLastOneByOpTaskid ($optaskid) {
        $cond = " and optaskid = :optaskid order by id desc";
        $bind = array(
            ":optaskid" => $optaskid,
        );
        return Dao::getEntityByCond("OpTaskOpNodeLog", $cond, $bind);
    }

}
