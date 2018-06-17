<?php

/*
 * AuditorPgroupRef
 */
class AuditorPgroupRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'auditorid',  // 运营id
            'pgroupid',  // pgroupid
            'status'); // 0：无效; 1：有效
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'auditorid',
            'pgroupid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["pgroup"] = array(
            "type" => "Pgroup",
            "key" => "pgroupid");
    }

    // $row = array();
    // $row["auditorid"] = $auditorid;
    // $row["pgroupid"] = $pgroupid;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditorPgroupRef::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["pgroupid"] = 0;
        $default["status"] = 1;

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
