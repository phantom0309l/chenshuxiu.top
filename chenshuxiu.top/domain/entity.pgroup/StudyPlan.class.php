<?php

/*
 * StudyPlan
 */
class StudyPlan extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'patientpgrouprefid',  // patientpgrouprefid
            'objtype',  // objtype
            'objid',  // objid
            'objcode',  // objcode
            'startdate',  // 开始时间
            'enddate',  // 结束时间
            'done_cnt',  // 做的次数
            'optaskid'); // optaskid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'patientpgrouprefid',
            'objid',
            'optaskid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["patientpgroupref"] = array(
            "type" => "PatientPgroupRef",
            "key" => "patientpgrouprefid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["optask"] = array(
            "type" => "OpTask",
            "key" => "optaskid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["patientpgrouprefid"] = $patientpgrouprefid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    // $row["startdate"] = $startdate;
    // $row["enddate"] = $enddate;
    // $row["done_cnt"] = $done_cnt;
    // $row["optaskid"] = $optaskid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "StudyPlan::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["patientpgrouprefid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["startdate"] = '';
        $default["enddate"] = '';
        $default["done_cnt"] = 0;
        $default["optaskid"] = 0;

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
