<?php

class DoctorResource extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',
            'content',
            'action',
            'method');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["content"] = '';
        $default["action"] = '';
        $default["method"] = '';

        $row += $default;
        return new self($row);
    }
}
