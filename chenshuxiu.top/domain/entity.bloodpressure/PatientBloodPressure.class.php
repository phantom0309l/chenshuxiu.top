<?php

/*
 * PatientBloodPressure
 */

class PatientBloodPressure extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'doctorid'    //doctorid
        , 'measured_time'    //测量时间
        , 'high'    //高压
        , 'low'    //低压
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'doctorid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["doctor"] = array("type" => "Doctor", "key" => "doctorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["measured_time"] = $measured_time;
    // $row["high"] = $high;
    // $row["low"] = $low;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "PatientBloodPressure::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["measured_time"] = '';
        $default["high"] = 0;
        $default["low"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
