<?php

/*
 * OptLog
 */
class OptLog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'optaskid',  // optaskid
            'auditorid',  // auditorid
            'domode',  // 跟进类型，初始化0，1：消息 2：电话
            'content',  // 任务具体内容
            'jsoncontent'); // 结构化数据
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'optaskid',
            'auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["optask"] = array(
            "type" => "OpTask",
            "key" => "optaskid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["optaskid"] = $optaskid;
    // $row["auditorid"] = $auditorid;
    // $row["domode"] = $domode;
    // $row["content"] = $content;
    // $row["jsoncontent"] = $jsoncontent;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OptLog::createByBiz row cannot empty");

        $default = array();
        $default["optaskid"] = 0;
        $default["auditorid"] = 0;
        $default["domode"] = 0;
        $default["content"] = '';
        $default["jsoncontent"] = '';

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
