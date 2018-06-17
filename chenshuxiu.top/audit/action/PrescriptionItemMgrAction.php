<?php

// PrescriptionItemMgrAction
class PrescriptionItemMgrAction extends AuditBaseAction
{

    public function doList () {
        $cond = '';
        $bind = [];
        $prescriptionitems = Dao::getEntityListByCond('PrescriptionItem', $cond, $bind);
        XContext::setValue("prescriptionitems", $prescriptionitems);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
