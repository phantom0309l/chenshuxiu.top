<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 2018/5/17
 * Time: 10:00
 */
class AuditorGroupRefService
{

    public static function updateBaseType(Auditor $auditor, $auditorGroupId) {
        $auditorGroups = AuditorGroupDao::getListByType('base');
        foreach ($auditorGroups as $auditorGroup) {
            $auditorGroupRef = AuditorGroupRefDao::getByAuditoridAuditorGroupid($auditor->id, $auditorGroup->id);
            if ($auditorGroupRef instanceof AuditorGroupRef) {
                $auditorGroupRef->remove();
            }
        }

        if (1 == $auditor->status && $auditorGroupId>0) {
            $auditorGroupRef = AuditorGroupRefDao::getByAuditoridAuditorGroupid($auditor->id, $auditorGroupId);
            if ($auditorGroupRef instanceof AuditorGroupRef) {
                $auditorGroupRef->unRemove();
            } else {
                $row = [
                    'auditorid' => $auditor->id,
                    'auditorgroupid' => $auditorGroupId
                ];
                AuditorGroupRef::createByBiz($row);
            }
        }
    }

    public static function updateAllOfAuditorGroup(AuditorGroup $auditorGroup, $auditorIdArr) {
        $auditorGroupRefs = AuditorGroupRefDao::getListByAuditorGroupid($auditorGroup->id);
        foreach ($auditorGroupRefs as $auditorGroupRef) {
            $auditorGroupRef->remove();
        }

        if (false == empty($auditorIdArr)) {
            foreach ($auditorIdArr as $auditorId) {
                $auditorGroupRef = AuditorGroupRefDao::getByAuditoridAuditorGroupid($auditorId, $auditorGroup->id);
                if ($auditorGroupRef instanceof AuditorGroupRef) {
                    $auditorGroupRef->unRemove();
                } else {
                    $row = [
                        'auditorid' => $auditorId,
                        'auditorgroupid' => $auditorGroup->id
                    ];
                    AuditorGroupRef::createByBiz($row);
                }
            }
        }
    }
}