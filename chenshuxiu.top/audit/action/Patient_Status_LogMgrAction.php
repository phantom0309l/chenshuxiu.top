<?php
// Patient_Status_LogMgrAction
class Patient_Status_LogMgrAction extends AuditBaseAction
{

    public function doList () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient_status_logs = Patient_Status_LogDao::getListByPatientid($patientid);

        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_status_logs', $patient_status_logs);

        return self::SUCCESS;
    }
}
