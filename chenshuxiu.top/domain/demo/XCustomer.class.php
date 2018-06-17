<?php

/*
 * XCustomer
 */
class XCustomer extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 姓名
            'mobile'); // 手机号
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["mobile"] = $mobile;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XCustomer::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["mobile"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getXOrderNum () {
        return XOrderDao::getXOrderNumByXCustomer($this);
    }
}
