<?php

/*
 * ADRMonitorRule
 * 药品不良反应监测规则
 */

class ADRMonitorRule extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'medicineid'    //medicineid
        , 'diseaseid'    //diseaseid
        , 'doctorid'    //doctorid
        , 'medicine_common_name'    //药品通用名称
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'medicineid', 'diseaseid', 'doctorid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["medicine"] = array("type" => "Medicine", "key" => "medicineid");
        $this->_belongtos["disease"] = array("type" => "Disease", "key" => "diseaseid");
        $this->_belongtos["doctor"] = array("type" => "Doctor", "key" => "doctorid");
    }

    // $row = array(); 
    // $row["medicineid"] = $medicineid;
    // $row["diseaseid"] = $diseaseid;
    // $row["doctorid"] = $doctorid;
    // $row["medicine_common_name"] = $medicine_common_name;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ADRMonitorRule::createByBiz row cannot empty");

        $default = array();
        $default["medicineid"] = 0;
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["medicine_common_name"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getItems() {
        return ADRMonitorRuleItemDao::getListByADRMRid($this->id);
    }
}
