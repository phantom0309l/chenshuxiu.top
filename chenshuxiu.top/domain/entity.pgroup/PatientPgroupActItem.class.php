<?php

/*
 * PatientPgroupActItem
 */
class PatientPgroupActItem extends Entity
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
            'pgroupid',
            'objtype',  // objtype
            'objid',  // objid
            'objcode',  // objcode
            'dealwithtplid',  // dealwithtplid
            'pushmsgid',  // pushmsgid
            'offsetdaycnt',  // 产生该次行为时与入组时间的差值
            'isnew',
            'isok');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientpgrouprefid',
            'objid',
            'dealwithtplid',
            'pushmsgid');
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
        $this->_belongtos["pgroup"] = array(
            "type" => "Pgroup",
            "key" => "pgroupid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["dealwithtpl"] = array(
            "type" => "DealwithTpl",
            "key" => "dealwithtplid");
        $this->_belongtos["pushmsg"] = array(
            "type" => "PushMsg",
            "key" => "pushmsgid");
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
    // $row["dealwithtplid"] = $dealwithtplid;
    // $row["pushmsgid"] = $pushmsgid;
    // $row["offsetdaycnt"] = $offsetdaycnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientPgroupActItem::createByBiz row cannot empty");

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["patientpgrouprefid"] = 0;
        $default["pgroupid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["dealwithtplid"] = 0;
        $default["pushmsgid"] = 0;
        $default["offsetdaycnt"] = 0;
        $default["isnew"] = 1;
        $default["isok"] = 0;

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
