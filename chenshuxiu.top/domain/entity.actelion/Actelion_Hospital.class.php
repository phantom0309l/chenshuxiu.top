<?php

/*
 * Actelion_Hospital
 */
class Actelion_Hospital extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //医院名称
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            );
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        
    }

    // $row = array(); 
    // $row["title"] = $title;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Actelion_Hospital::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
