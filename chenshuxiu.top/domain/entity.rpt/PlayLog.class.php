<?php
/*
 * PlayLog
 */
class PlayLog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'patientid',  // patientid
            'userid',  // userid
            'wxuserid',  // wxuserid
            'objtype',  // objtype
            'objid',  // objid
            'duration',  // 持续时长
            'total_duration'); // 资源时长
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'userid',
            'wxuserid',
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["userid"] = $userid;
    // $row["wxuserid"] = $wxuserid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["duration"] = $duration;
    // $row["total_duration"] = $total_duration;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PlayLog::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["userid"] = 0;
        $default["wxuserid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["duration"] = 0;
        $default["total_duration"] = 0;

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
