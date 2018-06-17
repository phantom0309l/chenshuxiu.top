<?php

/*
 * Doctor_WorkCalendar
 */

class Doctor_WorkCalendar extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'  // wxuserid
        , 'userid'  // userid
        , 'patientid'    //patientid
        , 'doctorid'    //doctorid
        , 'thedate'    //日期
        , 'content'    //数据JSON
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");

        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");

        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");

        $this->_belongtos["doctor"] = array("type" => "Doctor", "key" => "doctorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["thedate"] = $thedate;
    // $row["content"] = $content;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Doctor_WorkCalendar::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["thedate"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
