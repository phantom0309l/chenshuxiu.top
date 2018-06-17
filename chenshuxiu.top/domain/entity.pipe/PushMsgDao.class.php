<?php
/*
 * PushMsgDao
 */
class PushMsgDao extends Dao
{
    // 名称: getListByCronLogid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCronLogid ($cronlogid) {
        $cond = "AND objtype = 'CronLog' AND objid = :objid";

        $bind = [];
        $bind[':objid'] = $cronlogid;

        return Dao::getEntityListByCond("PushMsg", $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient ($patientid) {
        $cond = " AND patientid = :patientid order by id ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("PushMsg", $cond, $bind);
    }

    public static function getCntByObjtypeAndObjid ($send_by_objtype, $send_by_objid, $startTime, $endTime) {
        $sql = 'SELECT COUNT(*) FROM pushmsgs WHERE send_by_objtype=:objtype AND send_by_objid=:objid ';
        $bind = [];
        $bind[':objtype'] = $send_by_objtype;
        $bind[':objid'] = $send_by_objid;

        if ($startTime) {
            $sql .= ' AND (createtime = :startTime OR createtime > :startTime) ';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $sql .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }

        return Dao::queryValue($sql, $bind);
    }

    public static function getLastEntityByObjtypeAndObjidAndTimeSlot ($send_by_objtype, $send_by_objid, $startTime, $endTime) {
        $cond = " AND send_by_objtype=:objtype AND send_by_objid=:objid ";
        $bind = [];
        $bind[':objtype'] = $send_by_objtype;
        $bind[':objid'] = $send_by_objid;

        if ($startTime) {
            $cond .= ' AND (createtime = :startTime OR createtime > :startTime) ';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $cond .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }
        $cond .= ' ORDER BY createtime DESC LIMIT 1';
        return Dao::getEntityByCond('PushMsg', $cond, $bind);
    }
}
