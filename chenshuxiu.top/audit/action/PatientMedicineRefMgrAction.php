<?php

class PatientMedicineRefMgrAction extends AuditBaseAction
{
    // 列表
    public function doList () {
        return self::SUCCESS;
    }

    //commented by chenshigang@fangcunyisheng.com
    //public function doUpdateTargetPost(){
        //$patientid = XRequest::getValue("patientid", 0);
        //$patient = Patient::getById($patientid);

        //$patientmedicinerefs = PatientMedicineRefDao::getAllListByPatient($patient," and status = 1 ");

        //PatientMedicineTarget::removeAllByPatient($patient);

        //foreach ( $patientmedicinerefs as $a ) {
            //$row = [];
            //$row['patientid'] = $patientid;
            //$row['doctorid'] = $patient->doctorid;
            //$row['medicineid'] = $a->medicineid;
            //$row['drug_dose'] = $a->drug_dose;
            //$row['drug_frequency'] = $a->drug_frequency;
            //$row['createby'] = "Auditor";

            //PatientMedicineTarget::createByBiz($row);
        //}

        //XContext::setJumpPath("/patientmgr/drugdetail?patientid={$patientid}&preMsg=" . urlencode("已同步"));
        //return self::BLANK;
    //}
}
