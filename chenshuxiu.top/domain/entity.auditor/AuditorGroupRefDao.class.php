<?php

/*
 * AuditorGroupRefDao
 */

class AuditorGroupRefDao extends Dao
{

    public static function getAuditorIdsByAuditorGroupId($auditorGroupId) {
        $sql = "SELECT auditorid FROM auditorgrouprefs WHERE auditorgroupid = :auditorgroupid ";
        $bind = [
            ':auditorgroupid' => $auditorGroupId
        ];

        return Dao::queryValues($sql, $bind);
    }

    public static function getListByAuditorGroupid($auditorgroupid) {
        $cond = " and auditorgroupid = :auditorgroupid ";
        $bind = [
            ':auditorgroupid' => $auditorgroupid
        ];

        return Dao::getEntityListByCond('AuditorGroupRef', $cond, $bind);
    }

    public static function getByAuditoridAuditorGroupid($auditorid, $auditorgroupid) {
        $cond = " and auditorid = :auditorid and auditorgroupid = :auditorgroupid ";
        $bind = [
            ':auditorid' => $auditorid,
            ':auditorgroupid' => $auditorgroupid
        ];

        return Dao::getEntityByCond('AuditorGroupRef', $cond, $bind);
    }

    public static function getByTypeAndAuditorid($type, $auditorid) {
        $cond = " AND auditorid = :auditorid AND auditorgroupid in (SELECT id FROM auditorgroups WHERE type = :type) ";
        $bind = [
            ':type' => $type,
            ':auditorid' => $auditorid
        ];

        return Dao::getEntityByCond('AuditorGroupRef', $cond, $bind);
    }
}