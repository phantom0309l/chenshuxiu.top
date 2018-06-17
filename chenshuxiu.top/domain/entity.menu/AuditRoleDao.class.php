<?php
/*
 * AuditRoleDao
 */
class AuditRoleDao extends Dao
{
    public static function getByCode ($code) {
        $bind = [];
        $cond = " AND code = :code  ";
        $bind[':code'] = $code;

        return Dao::getEntityByCond("AuditRole", $cond, $bind);
    }
}
