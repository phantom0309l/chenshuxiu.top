<?php

class DoctordbOplog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',
            'doctorname',
            'userid',
            'username',
            'patientid',
            'objtype',
            'objid',
            'content');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["doctorname"] = $doctorname;
    // $row["userid"] = $userid;
    // $row["username"] = $username;
    // $row["patientid"] = $patientid;
    // $row["content"] = $content;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["doctorname"] = '';
        $default["userid"] = '';
        $default["username"] = '';
        $default["patientid"] = '';
        $default["content"] = '';
        $default["objtype"] = '';
        $default["objid"] = '';

        $row += $default;
        return new self($row);
    }
}
