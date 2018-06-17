<?php

// PatientMedicineSheetItemMgrAction
class PatientMedicineSheetItemMgrAction extends AuditBaseAction
{

    public function doModifyPost () {
        $myauditor = $this->myauditor;

        $pmsiid = XRequest::getValue("patientmedicinesheetitemid", 0);
        $drug_dose = XRequest::getValue("drug_dose", '');
        $drug_frequency = XRequest::getValue("drug_frequency", '');
        $status = XRequest::getValue("status", '');
        $auditremark = XRequest::getValue("auditremark", '');

        $pmsi = PatientMedicineSheetItem::getById($pmsiid);

        $now = date('Y-m-d H:i:s');
        $pmsi->auditlog .= "{$now} {$myauditor->name} 操作 {$pmsi->drug_dose}=>{$drug_dose} {$pmsi->drug_frequency}=>{$drug_frequency} {$pmsi->status}=>{$status} {$pmsi->auditremark}=>{$auditremark}";

        $pmsi->drug_dose = $drug_dose;
        $pmsi->drug_frequency = $drug_frequency;
        $pmsi->auditremark = $auditremark;
        $pmsi->status = $status;

        echo 'ok';

        return self::BLANK;
    }
}
