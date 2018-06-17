<?php

/*
 * Rpt_week_doctor_patient
 */
class Rpt_week_doctor_patient extends Entity
{

    protected function init_database () {
        $this->database = 'statdb';
    }

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'begindate',  // 开始日期
            'enddate',  // 结束日期
            'scancnt',  // 扫码人数
            'baodaocnt',  // 报到人数
            'pipe_pcnt'); // 有流,人数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["begindate"] = $begindate;
    // $row["enddate"] = $enddate;
    // $row["scancnt"] = $scancnt;
    // $row["baodaocnt"] = $baodaocnt;
    // $row["pipe_pcnt"] = $pipe_pcnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_week_doctor_patient::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["begindate"] = '0000-00-00';
        $default["enddate"] = '0000-00-00';
        $default["scancnt"] = 0;
        $default["baodaocnt"] = 0;
        $default["pipe_pcnt"] = 0;

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
