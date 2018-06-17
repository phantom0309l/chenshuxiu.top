<?php

/*
 * AuditorPushMsgTpl
 */
class AuditorPushMsgTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title',  // title
            'ename',  // ename
            'content'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["ename"] = $ename;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditorMonitorType::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["ename"] = '';
        $default["content"] = '';

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
