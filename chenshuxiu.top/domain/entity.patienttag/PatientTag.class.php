<?php

/*
 * PatientTag
 */
class PatientTag extends Entity
{

    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
            'patientid', // patientid
            'doctorid', // doctorid
            'patienttagtplid', // patienttagtplid
            'init_name'
        ); // 创建时的标签名字快照，不能修改
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array(
            'patientid',
            'doctorid',
            'patienttagtplid'
        );
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid"
        );
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid"
        );
        $this->_belongtos["patienttagtpl"] = array(
            "type" => "PatientTagTpl",
            "key" => "patienttagtplid"
        );
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["patienttagtplid"] = $patienttagtplid;
    // $row["init_name"] = $init_name;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "PatientTag::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["patienttagtplid"] = 0;
        $default["init_name"] = '';

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
