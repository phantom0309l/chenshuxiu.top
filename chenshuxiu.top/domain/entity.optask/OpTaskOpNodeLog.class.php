<?php

/*
 * OpTaskOpNodeLog
 */
class OpTaskOpNodeLog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'optaskid',  // optaskid
            'opnodeid',  // opnodeid
            'type',  // create 创建, manual 手动, timeout 超时
            'auditorid',  // auditorid
            'remark'); // remark
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'optaskid',
            'opnodeid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["optask"] = array(
            "type" => "OpTask",
            "key" => "optaskid");
        $this->_belongtos["opnode"] = array(
            "type" => "OpNode",
            "key" => "opnodeid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["optaskid"] = $optaskid;
    // $row["opnodeid"] = $opnodeid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpTaskOpNodeLog::createByBiz row cannot empty");

        $default = array();
        $default["optaskid"] = 0;
        $default["opnodeid"] = 0;
        $default["type"] = '';
        $default["auditorid"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
