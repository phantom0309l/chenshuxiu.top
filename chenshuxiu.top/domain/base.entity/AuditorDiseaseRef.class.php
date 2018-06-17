<?php

/*
 * AuditorDiseaseRef
 */
class AuditorDiseaseRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'auditorid',  // 运营id
            'diseaseid'); // diseaseid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'auditorid',
            'diseaseid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
    }

    // $row = array();
    // $row["auditorid"] = $auditorid;
    // $row["diseaseid"] = $diseaseid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditorDiseaseRef::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["diseaseid"] = 0;

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
