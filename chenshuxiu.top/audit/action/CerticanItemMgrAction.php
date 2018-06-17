<?php
// CerticanItemMgrAction
class CerticanItemMgrAction extends AuditBaseAction
{

    public function doList () {
        $certicanid = XRequest::getValue('certicanid', 0);
        $certican = Certican::getById($certicanid);
        DBC::requireNotEmpty($certican, "certican is null");

        $certicanitems = CerticanItemDao::getListByCertican($certican);

        foreach ($certicanitems as $a) {
            $a->fixWbc();
        }

        XContext::setValue('certican', $certican);
        XContext::setValue('certicanitems', $certicanitems);

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
