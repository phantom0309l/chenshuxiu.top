<?php

/*
 * Rpt_patient
 */
class Rpt_week_doctor_data extends Entity
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
        return [
            'doctorid',
            'diseaseid',
            'weekend_date',
            'data',
        ];
    }

    protected function init_keys_lock () {
        $this->_keys_lock = [
            'doctorid',
        ];
    }

    protected function init_belongtos () {
        $this->_belongtos = [];
        $this->_belongtos["doctor"] = [
            "type" => "Doctor",
            "key" => "doctorid",
        ];
        $this->_belongtos["disease"] = [
            "type" => "Disease",
            "key" => "diseaseid",
        ];
    }
    /*
    $row = [];
    $row['doctorid'] = $doctorid;
    $row['diseaseid'] = $diseaseid;
    $row['weekend_date'] = $weekend_date;
    $row['data'] = $data;
     */

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = [];
        $default['doctorid'] = 0;
        $default['diseaseid'] = 0;
        $default['weekend_date'] = '';
        $default['data'] = '';

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
