<?php

/*
 * DoctorConfig
 */
class DoctorConfig extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'doctorconfigtplid',  // doctorconfigtplid
            'status') // 开关状态
;
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid',
            'doctorconfigtplid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["doctorconfigtpl"] = array(
            "type" => "DoctorConfigTpl",
            "key" => "doctorconfigtplid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["doctorconfigtplid"] = $doctorconfigtplid;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorConfig::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["doctorconfigtplid"] = 0;
        $default["status"] = 0;

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
