<?php

/*
 * PatientTodayMarkTpl
 */
class PatientTodayMarkTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseasegroupid'    //疾病组ID
        ,'title'    //名称
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
             );
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        
    $this->_belongtos["diseasegroup"] = array ("type" => "DiseaseGroup", "key" => "diseasegroupid" );
    }

    // $row = array(); 
    // $row["diseasegroupid"] = $diseasegroupid;
    // $row["title"] = $title;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientTodayMarkTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseasegroupid"] =  0;
        $default["title"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
