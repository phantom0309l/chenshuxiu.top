<?php

class PatientStatusService
{
    public static function getPatientStatusDescArray () {
        return array(
            'all' => '全部',
            '0001' => '待运营审核',
            '1101' => '待医生审核',
            '1111' => '在线患者',
            '0201' => '运营拒绝/下线',
            '0121' => '医生拒绝/下线',
            '0211' => '运营/合并下线',
            '0110' => '患者死亡');
    }

    // 患者状态描述
    public static function getPatientStatusDesc (Patient $patient) {
        $statusStr = $patient->getPatientStatusStr();
        $arr = self::getPatientStatusDescArray();
        $str = $arr[$statusStr];
        if (empty($str)) {
            Debug::warn("PatientStatusService::getPatientStatusDesc[{$patient->id}][{$statusStr}]");
            $str = $statusStr;
        }

        if ($patient->doubt_type == 1) {
            $str .= "[无效]";
        }

        $str .= "[{$patient->subscribe_cnt} / {$patient->wxuser_cnt}]";

        return $str;
    }

    // 记录patient状态变化的日志
    private static function createPatientStatusLog (Patient $patient, $patient_status_old_json, $content) {
        $row = array();
        $row['patientid'] = $patient->id;
        $row['patient_status_json'] = $patient->getJsonPatientStatus();
        $row['patient_status_old_json'] = $patient_status_old_json;
        $row['content'] = $content;

        $patientstatuslog = Patient_Status_Log::createByBiz($row);

        self::checkIs_see($patient);
    }

    // 系统自动通过
    public static function auto_pass (Patient $patient) {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->auditstatus, "auditstatus!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "系统自动审核通过";

        $patient->set4lock('status', 1);
        $patient->set4lock('auditstatus', 1);
        if ($patient->doctorid == 33) {
            $patient->set4lock('doctor_audit_status', 0);
        } else {
            $patient->set4lock('doctor_audit_status', 1);
        }

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 系统自动审核未通过
    public static function auto_refuse (Patient $patient) {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->auditstatus, "auditstatus!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "系统自动审核未通过，需要运营审核";

        $patient->set4lock('status', 0);
        $patient->set4lock('auditstatus', 0);
        $patient->set4lock('doctor_audit_status', 0);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 运营审核通过
    public static function auditor_pass (Patient $patient, Auditor $auditor) {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");
        DBC::requireEquals(0, $patient->auditstatus, "auditstatus!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "运营审核通过:{$auditor->name}";

        $patient->set4lock('status', 1);
        $patient->set4lock('auditstatus', 1);
        if ($patient->doctorid == 33) {
            $patient->set4lock('doctor_audit_status', 0);
        } else {
            $patient->set4lock('doctor_audit_status', 1);
        }

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 运营审核拒绝
    public static function auditor_refuse (Patient $patient, Auditor $auditor, $auditremark = "") {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");
        DBC::requireEquals(0, $patient->auditstatus, "auditstatus!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "运营审核拒绝:{$auditor->name}";

        $patient->set4lock('status', 0);
        $patient->set4lock('auditstatus', 2);

        $patient->auditremark .= $auditremark;

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 运营上线,（运营后台后悔了,重新上线）
    public static function auditor_online (Patient $patient, Auditor $auditor, $auditremark = "") {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}] [auditorid={$auditor->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "运营上线:{$auditor->name}";

        $patient->set4lock('status', 1);
        $patient->set4lock('auditstatus', 1);
        $patient->set4lock('doctor_audit_status', 1);

        $patient->auditremark .= $auditremark;

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 运营下线（运营后台后悔了,重新下线）
    public static function auditor_offline (Patient $patient, Auditor $auditor, $auditremark = "") {
        DBC::requireTrue($patient->getPatientStatusStr() != '0110', "死亡患者不能再下线");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "运营下线:{$auditor->name}";

        // doctor_audit_status 不改
        $patient->set4lock('status', 0);
        $patient->set4lock('auditstatus', 2);

        $patient->auditremark .= $auditremark;

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 患者死亡 0110
    public static function auditor_dead (Patient $patient, Auditor $auditor, $auditremark = "") {
        DBC::requireEquals(1, $patient->status, "status!=1 [patientid={$patient->id}] [auditorid={$auditor->id}]");
        DBC::requireEquals(1, $patient->is_live, "is_live!=1 [patientid={$patient->id}] [auditorid={$auditor->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "患者死亡:{$auditor->name}";

        $patient->set4lock('status', 0);
        $patient->set4lock('auditstatus', 1);
        $patient->set4lock('doctor_audit_status', 1);
        $patient->set4lock('is_live', 0);
        $patient->auditremark .= $auditremark;

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 患者复活
    public static function reLive (Patient $patient, Auditor $auditor,  $auditremark = "") {
        DBC::requireEquals(0, $patient->status, "status!=1 [patientid={$patient->id}] [auditorid={$auditor->id}]");
        DBC::requireEquals(0, $patient->is_live, "is_live!=1 [patientid={$patient->id}] [auditorid={$auditor->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "患者复活:{$auditor->name}";

        $patient->set4lock('status', 1);
        $patient->set4lock('auditstatus', 1);
        $patient->set4lock('is_live', 1);
        $patient->auditremark .= $auditremark;

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 医生审核通过
    public static function doctor_pass (Patient $patient, Doctor $doctor) {
        DBC::requireEquals(1, $patient->status, "status!=1 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "医生审核通过:{$doctor->name}";

        $patient->set4lock('doctor_audit_status', 1);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 医生审核拒绝
    public static function doctor_refuse (Patient $patient, Doctor $doctor) {
        DBC::requireEquals(1, $patient->status, "status!=1 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "医生审核拒绝:{$doctor->name}";

        $patient->set4lock('status', 0);
        $patient->set4lock('doctor_audit_status', 2);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 医生录入
    public static function doctor_add (Patient $patient, Doctor $doctor) {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->auditstatus, "auditstatus!=0 [patientid={$patient->id}]");
        DBC::requireEquals(0, $patient->doctor_audit_status, "doctor_audit_status!=0 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "医生录入:{$doctor->name}";

        $patient->set4lock('status', 1);
        $patient->set4lock('auditstatus', 1);
        $patient->set4lock('doctor_audit_status', 1);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 医生上线
    public static function doctor_online (Patient $patient, Doctor $doctor) {
        DBC::requireEquals(0, $patient->status, "status!=0 [patientid={$patient->id}]");
        DBC::requireEquals(2, $patient->doctor_audit_status, "doctor_audit_status!=2 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "医生上线:{$doctor->name}";

        $patient->set4lock('status', 1);
        $patient->set4lock('doctor_audit_status', 1);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 医生下线
    public static function doctor_offline (Patient $patient, Doctor $doctor) {
        DBC::requireEquals(1, $patient->status, "status!=1 [patientid={$patient->id}]");
        DBC::requireEquals(1, $patient->doctor_audit_status, "doctor_audit_status!=1 [patientid={$patient->id}]");

        $patient_status_old_json = $patient->getJsonPatientStatus();
        $str = "医生下线:{$doctor->name}";

        $patient->set4lock('status', 0);
        $patient->set4lock('doctor_audit_status', 2);

        self::createPatientStatusLog($patient, $patient_status_old_json, $str);
    }

    // 修改患者状态是,修改is_see字段
    public static function checkIs_see (Patient $patient) {
        if ($patient->status == 1 || ($patient->status == 0 && $patient->auditstatus == 0)) {
            $patient->is_see = 1;
        } else {
            $patient->is_see = 0;
        }
    }
}
