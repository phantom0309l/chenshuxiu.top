<?php
// Dc_patientPlanItemMgrAction
class Dc_patientPlanItemMgrAction extends AuditBaseAction
{

    public function doList () {
        $dc_patientplanid = XRequest::getValue('dc_patientplanid', 0);
        $dc_patientplan = Dc_patientPlan::getById($dc_patientplanid);

        $dc_patientplanitems = Dao::getEntityListByCond('Dc_patientPlanItem', " and dc_patientplanid = :dc_patientplanid ", [':dc_patientplanid' => $dc_patientplanid]);

        XContext::setValue('dc_patientplan', $dc_patientplan);
        XContext::setValue('dc_patientplanitems', $dc_patientplanitems);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
