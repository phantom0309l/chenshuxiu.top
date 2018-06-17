<?php

/*
 * AuditorGroup
 */

class AuditorGroup extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'type'
            ,'ename'
            ,'name'
        ); // 组名
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["name"] = $name;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "AuditorGroup::createByBiz row cannot empty");

        $default = array();
        $default["type"] = '';
        $default["ename"] = '';
        $default["name"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getAuditors() {
        return AuditorDao::getListByGroupid($this->id);
    }

    public static function getTypeArr($needAll = false) {
        $arr = [];
        if($needAll){
            $arr['all'] = '全部';
        }
        $arr['base'] = 'base';
        $arr['auditor'] = 'auditor';
        $arr['market'] = 'market';

        return $arr;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
