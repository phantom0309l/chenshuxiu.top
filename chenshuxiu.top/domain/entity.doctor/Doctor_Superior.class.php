<?php

/*
 * Doctor_Superior
 */
class Doctor_Superior extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',//医生id
            'superior_doctorid',//主管医生id
        ); 
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["superior_doctor"] = array(
            "type" => "Doctor",
            "key" => "superior_doctorid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["superior_doctorid"] = $superior_doctorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default['doctorid'] = '';
        $default['superior_doctorid'] = '';

        $row += $default;
        return new self($row);
    }
}
