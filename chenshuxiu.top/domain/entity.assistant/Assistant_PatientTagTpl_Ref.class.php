<?php

class Assistant_PatientTagTpl_Ref extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'assistantid',  //助理账号id
            'patienttagtplid'); // 患者标签id
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "Assistant",
            "key" => "assistantid");
        $this->_belongtos["patienttagtpl"] = array(
            "type" => "PatientTagTpl",
            "key" => "patienttagtplid");
    }

    // $row = array();
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["assistantid"] = '';
        $default["patienttagtplid"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
