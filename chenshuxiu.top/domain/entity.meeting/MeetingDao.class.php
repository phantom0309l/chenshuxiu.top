<?php
/*
 * MeetingDao
 */
class MeetingDao extends Dao
{
    // 名称: getByCallSid
    // 备注:
    // 创建:
    // 修改:
    public static function getByCallSid ($callsid) {
        $bind = [];
        $cond = " AND callsid=:callsid ";
        $bind[':callsid'] = $callsid;
        return self::getEntityByCond("Meeting", $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient ($patientid) {
        $bind = [];
        $cond = " AND patientid = :patientid order by starttime desc ";
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("Meeting", $cond, $bind);
    }
}
