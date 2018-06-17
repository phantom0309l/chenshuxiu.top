<?php
// PrescriptionMgrAction
class PrescriptionMgrAction extends AuditBaseAction
{

    public function doList () {
        $prescriptions = Dao::getEntityListByCond('Prescription');
        XContext::setValue("prescriptions", $prescriptions);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $patient = $this->myuser->patient;

        $row = [];
        $row["wxuserid"] = 0;
        $row["userid"] = 0;
        $row["patientid"] = $patient->id;
        $row["shoporderid"] = 4;
        $row["yishiid"] = 5;
        $row["yaoshiid_audit"] = 6;
        $row["yaoshiid_send"] = 7;
        $row["yishi_remark"] = 'yishi_remark';
        $row["content"] = 'content';
        $row["time_audit"] = '0000-00-00';
        $row["time_send"] = '0000-00-00';
        $row["md5str"] = 'md5str';
        $row["status"] = 1;
        $row["remark"] = 'remark';
        $prescription = Prescription::createByBiz($row);

        $medicineProducts = Dao::getEntityListByCond('MedicineProduct');

        foreach ($medicineProducts as $a) {
            $row = [];
            $row["prescriptionid"] = $prescription->id;
            $row["medicineproductid"] = $a->id;
            $row["medicine_title"] = $a->getTitle();
            $row["drug_way"] = $a->drug_way;
            $row["drug_dose"] = $a->drug_dose;
            $row["drug_frequency"] = $a->drug_frequency;
            $row["cnt"] = rand(1, 9);
            $row["content"] = '医生说明';
            PrescriptionItem::createByBiz($row);
        }

        return self::SUCCESS;
    }

    public function doOne () {
        $prescriptionid = XRequest::getValue('prescriptionid', 0);

        $prescription = Prescription::getById($prescriptionid);

        $prescriptionitems = $prescription->getPrescriptionItems();

        XContext::setValue("prescription", $prescription);
        XContext::setValue("prescriptionitems", $prescriptionitems);
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
