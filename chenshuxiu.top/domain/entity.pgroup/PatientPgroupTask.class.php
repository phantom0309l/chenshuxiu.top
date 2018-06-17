<?php

/*
 * PatientPgroupTask
 */
class PatientPgroupTask extends Entity
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
            'pgroupid',  // pgroupid
            'objtype',  // objtype
            'objid',  // objid
            'objcode',  // objcode
            'status',  // 状态，0：初始化，未完成，1：已完成，2：未完成，由系统置成的状态
            'nextfollowtime'); // nextfollowtime
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'pgroupid',
            'objid');
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
        $this->_belongtos["pgroup"] = array(
            "type" => "Pgroup",
            "key" => "pgroupid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["pgroupid"] = $pgroupid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    // $row["status"] = $status;
    // $row["nextfollowtime"] = $nextfollowtime;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientPgroupTask::createByBiz row cannot empty");

        if ($row["wxuserid"] == null) {
            $row["wxuserid"] = 0;
        }

        if ($row["userid"] == null) {
            $row["userid"] = 0;
        }

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
        $default["pgroupid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["status"] = 0;
        $default["nextfollowtime"] = '0000-00-00 00:00:00';

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
