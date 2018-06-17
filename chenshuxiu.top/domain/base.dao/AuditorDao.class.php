<?php

/*
 * AuditorDao
 */

class AuditorDao extends Dao
{
    // 名称: getBaodaoByWoy
    // 备注:
    // 创建:
    // 修改:
    public static function getBaodaoByWoy($auditorid, $woy) {
        $cond = '';
        $bind = [];

        if ($auditorid != 0) {
            $cond = " AND b.auditorid_market = :auditorid_market ";
            $bind[':auditorid_market'] = $auditorid;
        }

        // TODO by sjp 20170505 diseaseid = 1 ? 只有多动症用?
        $sql = "SELECT count( distinct a.id )
            FROM patients as a
            INNER JOIN pcards x on x.patientid = a.id
            INNER JOIN doctors b ON b.id = x.doctorid
            WHERE 1=1 AND a.auditstatus = 1 $cond AND a.woy = :woy AND x.diseaseid = 1";

        $bind[':woy'] = $woy;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getByUserid
    // 备注:
    // 创建:
    // 修改:
    public static function getByUserid($userid) {
        $cond = " AND userid = :userid order by id ";

        $bind = [];
        $bind[':userid'] = $userid;

        return Dao::getEntityByCond('Auditor', $cond, $bind);
    }

    // 名称: getNotBaodaoByWoy
    // 备注:
    // 创建:
    // 修改:
    public static function getNotBaodaoByWoy($auditorid, $woy) {
        $bind = [];

        if ($auditorid != 0) {
            $cond = " AND c.auditorid_market = :auditorid_market  ";
            $bind[':auditorid_market'] = $auditorid;
        }

        $sql = "SELECT count(*)
            FROM wxusers a
            LEFT JOIN users b ON a.userid = b.id
            INNER JOIN doctors c ON a.doctorid = c.id
            WHERE 1=1 $cond  AND a.woy = :woy AND b.patientid = 0 ";

        $bind[':woy'] = $woy;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByStatus
    // 备注: 在职 或 离职
    public static function getListByStatus($status) {
        $cond = " AND status = :status";

        $bind = [];
        $bind[':status'] = $status;
        return Dao::getEntityListByCond('Auditor', $cond, $bind);
    }


    public static function getListByGroupid($auditorgroupid) {
        $cond = " and id in (
                    select auditorid
                    from auditorgrouprefs 
                    where auditorgroupid = :auditorgroupid 
                ) ";
        $bind = [
            ':auditorgroupid' => $auditorgroupid
        ];

        return Dao::getEntityListByCond("Auditor", $cond, $bind);
    }

    public static function getNamesByAuditorGroup(AuditorGroup $auditorGroup) {
        $sql = "select a.name
                from auditors a
                inner join auditorgrouprefs b on b.auditorid = a.id
                where b.auditorgroupid = :auditorgroupid ";
        $bind = [
            ':auditorgroupid' => $auditorGroup->id
        ];

        return Dao::queryValues($sql, $bind);
    }

    public static function getListByAuditorGroup(AuditorGroup $auditorGroup) {
        $sql = "select a.*
                from auditors a
                inner join auditorgrouprefs b on b.auditorid = a.id
                where b.auditorgroupid = :auditorgroupid ";
        $bind = [
            ':auditorgroupid' => $auditorGroup->id
        ];

        return Dao::loadEntityList('Auditor', $sql, $bind);
    }

    public static function getByCdr_no1 ($cdr_no1) {
        $cond = " AND cdr_no1 = :cdr_no1";
        $bind = [];
        $bind[':cdr_no1'] = $cdr_no1;

        return Dao::getEntityByCond('Auditor', $cond, $bind);
    }

    public static function getByCdr_no2 ($cdr_no2) {
        $cond = " AND cdr_no2 = :cdr_no2";
        $bind = [];
        $bind[':cdr_no2'] = $cdr_no2;

        return Dao::getEntityByCond('Auditor', $cond, $bind);
    }
}
