<?php
/*
 * AuditMenuDao
 */
class AuditMenuDao extends Dao
{
    public static function getParentMenuListByAuditor (Auditor $auditor) {
        $auditroleidarr = $auditor->getAuditRoleIdArr();
        $parentMenus = AuditMenuDao::getParentList();
        $ret = [];
        foreach ($parentMenus as $a) {
            if($auditor->canVisitAuditResource($a->auditresource)){
                $ret[] = $a;
            }
        }
        return $ret;
    }

    public static function getListByParentmenuid ($parentmenuid) {
        $cond = "AND parentmenuid=:parentmenuid order by pos asc";
        $bind = array(':parentmenuid' => $parentmenuid);
        return Dao::getEntityListByCond("AuditMenu", $cond, $bind);
    }

    public static function getParentList() {
        return self::getListByParentmenuid(0);
    }

}
