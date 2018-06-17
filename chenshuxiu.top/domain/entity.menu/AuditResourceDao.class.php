<?php
/*
 * AuditResourceDao
 */
class AuditResourceDao extends Dao
{
    public static function getByActionMethod ( $action, $method ){
        $cond = "AND action=:action And method=:method";
        $bind = array(
            ':action' => $action,
            ':method' => $method
        );
        return Dao::getEntityByCond("AuditResource", $cond, $bind);
    }

    public static function getListByAuditmenuid ( $auditmenuid ){
        $cond = " AND auditmenuid=:auditmenuid ";
        $bind = array(
            ':auditmenuid' => $auditmenuid
        );
        return Dao::getEntityListByCond("AuditResource", $cond, $bind);
    }
}
