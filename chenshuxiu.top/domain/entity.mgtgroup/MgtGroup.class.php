<?php

/*
 * MgtGroup
 */

class MgtGroup extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'mgtgrouptplid'    //mgtgrouptplid
        , 'objtype'    //objtype
        , 'objid'    //objid
        , 'startdate'    //入组时间
        , 'enddate'    //出组时间
        , 'status'    //0：待加入分组；1：正在组中；2：顺利出组
        , 'pos'    //第几组
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('wxuserid', 'userid', 'patientid', 'mgtgrouptplid', 'objid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array("type" => "Wxuser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["mgtgrouptpl"] = array("type" => "Mgtgrouptpl", "key" => "mgtgrouptplid");
        $this->_belongtos["obj"] = array("type" => "Obj", "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["mgtgrouptplid"] = $mgtgrouptplid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["startdate"] = $startdate;
    // $row["enddate"] = $enddate;
    // $row["status"] = $status;
    // $row["pos"] = $pos;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "MgtGroup::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["mgtgrouptplid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["startdate"] = '';
        $default["enddate"] = '';
        $default["status"] = 0;
        $default["pos"] = 0;

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
    