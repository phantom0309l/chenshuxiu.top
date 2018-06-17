<?php
/*
 * CdrMeetingDao
 */
class CdrMeetingDao extends Dao
{
    public static function getByCdr_Main_Unique_Id ($cdr_main_unique_id) {
        $cond = " and cdr_main_unique_id = :cdr_main_unique_id ";
        $bind = [];
        $bind[':cdr_main_unique_id'] = $cdr_main_unique_id;

        return Dao::getEntityByCond('CdrMeeting', $cond, $bind);
    }

    public static function getListByPatientidAuditorid ($patientid, $auditorid, $condEx="" ) {
        $cond = " and patientid = :patientid and auditorid = :auditorid " . $condEx;
        $bind = [];

        $bind[':patientid'] = $patientid;
        $bind[':auditorid'] = $auditorid;

        return Dao::getEntityListByCond('CdrMeeting', $cond, $bind);
    }

    public static function getListByPatientid ($patientid, $condEx="" ) {
        $cond = " and patientid = :patientid " . $condEx;
        $bind = [];

        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond('CdrMeeting', $cond, $bind);
    }


    // 根据 $auditor_id ,cdr_call_type 和 status 获取 cdrMeeting 的个数
    public static function getCntByAuditorIdAndCallTypeAndStatusAndCreateTime ($auditor_id, Array $callType, Array $status, $startTime=null, $endTime=null) {
        $sql = 'SELECT COUNT(*) AS cnt FROM cdrmeetings WHERE 1=1 AND auditorid=:auditorid ';
        $bind = [];
        $bind[':auditorid'] = $auditor_id;

        if(!empty($callType)) {
            $callTypeStr = implode(',', $callType);
            $sql .= ' AND cdr_call_type IN (:callType) ';
            $bind[':callType'] = $callTypeStr;
        }

        if (!empty($status)) {
            $statusStr = implode(',', $status);
            $sql .= ' AND cdr_status IN (:status) ';
            $bind[':status'] = $statusStr;
        }

        if ($startTime) {
            $sql .= ' AND createtime >= :startTime';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $sql .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }

        return Dao::queryValue($sql, $bind);
    }

    // 根据 座席接通时长获取 cdrMeeting 的个数
    public static function getCntByAnswerTimeAndCreateTime ($auditor_id, $begin=null, $end=null, $startTime=null, $endTime=null) {
        $sql = 'SELECT COUNT(*) FROM cdrmeetings WHERE 1=1 AND auditorid=:auditorid ';
        $bind = [];
        $bind[':auditorid'] = $auditor_id;

        if ($begin) {
            $sql .= ' AND cdr_end_time-cdr_answer_time > :begin ';
            $bind[':begin'] = $begin;
        }

        if ($end) {
            $sql .= ' AND cdr_end_time-cdr_answer_time<=:end ';
            $bind[':end'] = $end;
        }

        if ($startTime) {
            $sql .= ' AND createtime >= :startTime ';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $sql .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }

        return Dao::queryValue($sql, $bind);
    }

    // 查询某个运营在一个时间段的通话时长
    // 可根据 cdr_call_type 和 cdr_status 进行筛选
    public static function getAvgCallTimeByAuditorIdAndCallTypeAndStatus ($auditor_id, $callType, Array $status, $startTime=null, $endTime=null) {
        if(CdrMeetingService::isCallIn($callType)) {
            $sql = 'SELECT AVG(cdr_end_time-cdr_answer_time) FROM cdrmeetings WHERE 1=1 ';
        }else {
            $sql = 'SELECT AVG(cdr_end_time-cdr_bridge_time) FROM cdrmeetings WHERE 1=1 ';
        }

        $sql .= ' AND auditorid=:auditorid AND cdr_call_type=:callType ';
        $bind = [];
        $bind[':auditorid'] = $auditor_id;
        $bind[':callType'] = $callType;

        if (!empty($status)) {
            $statusStr = implode(',', $status);
            $sql .= ' AND cdr_status IN (:status) ';
            $bind[':status'] = $statusStr;
        }

        if ($startTime) {
            $sql .= ' AND createtime >= :startTime ';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $sql .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }
        return Dao::queryValue($sql, $bind);
    }

    // 获取运行在某个时间段内最后一次的 cdrmetting
    public static function getLastEntityByAuditorAndTimeSlot ($auditor_id, $startTime, $endTime) {
        $cond = " and auditorid = :auditorid ";
        $bind = [];
        $bind[':auditorid'] = $auditor_id;

        if ($startTime) {
            $cond .= ' AND createtime >= :startTime ';
            $bind[':startTime'] = $startTime;
        }

        if ($endTime) {
            $cond .= ' AND createtime < :endTime ';
            $bind[':endTime'] = $endTime;
        }
        $cond .= ' ORDER BY createtime DESC LIMIT 1';
        return Dao::getEntityByCond('CdrMeeting', $cond, $bind);
    }
}
