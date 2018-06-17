<?php

/*
 * AuditorMonitorRefType
 */
class AuditorPushMsgTplRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'auditorid',  // auditorid
            'auditorpushmsgtplid',  // auditorpushmsgtplid
            'can_ops'); // 能够发监控消息 0:不可以；1:可以
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'auditorid',
            'auditorpushmsgtplid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["auditorpushmsgtpl"] = array(
            "type" => "AuditorPushMsgTpl",
            "key" => "auditorpushmsgtplid");
    }

    // $row = array();
    // $row["auditorid"] = $auditorid;
    // $row["auditorpushmsgtplid"] = $auditorpushmsgtplid;
    // $row["can_ops"] = $can_ops;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditorMonitorRefType::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["auditorpushmsgtplid"] = 0;
        $default["can_ops"] = 0;

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
