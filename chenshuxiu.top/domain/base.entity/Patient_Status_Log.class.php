<?php

/*
 * Patient_Status_Log
 */
class Patient_Status_Log extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'patientid',  // patientid
            'patient_status_json',  // 新状态,json
            'patient_status_old_json',  // 旧状态,json
            'content'); // 操作行为描述
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["patient_status_json"] = $patient_status_json;
    // $row["patient_status_old_json"] = $patient_status_old_json;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Patient_Status_Log::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["patient_status_json"] = '';
        $default["patient_status_old_json"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
}
