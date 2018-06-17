<?php

class ChufangOplog extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'prescriptionid',
            'yishiid',
            'yishi_name',
            'content');
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["yishi"] = array(
            "type" => "YiShi",
            "key" => "yishiid");

        $this->_belongtos["prescription"] = array(
            "type" => "Prescription",
            "key" => "prescriptionid");
    }

    //$row = array();
    //$row['prescriptionid'] = $prescriptionid;
    //$row['yishiid'] = $yishiid;
    //$row['yishi_name'] = $yishi_name;
    //$row['content'] = $content;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["prescriptionid"] = '';
        $default["yishiid"] = 0;
        $default["yishi_name"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }
}
