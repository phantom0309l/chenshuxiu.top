<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-5-28
 * Time: 下午4:45
 *
 *
 * Rpt_patient_month_settle
 */
class Rpt_patient_month_settle extends Entity
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
            'patientid',  // patientid
            'isscan',  // isscan是否扫码，0：非扫码；1：扫码
            'patientstatus',  // patientstatus患者状态,0:无效；1：有效；2：取关
            'patientdaycnt',  // patientdaycnt患者管理时长（天）
            'doctorid',  // doctorid
            'themonth',  // 收益统计年，月份
            'baodaodate',  // 冗余报到时间
            'pipecntbypatient',  // 报到时间到统计时的时间间隔
            'month_pos'); // 是否活跃0：不活跃;1：活跃
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'doctorid',
            'themonth',
            'baodaodate');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_patient_month_settle::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["isscan"] = 0;
        $default["patientstatus"] = 0;
        $default["patientdaycnt"] = "";
        $default["doctorid"] = 0;
        $default["themonth"] = '0000-00-00';
        $default["baodaodate"] = '0000-00-00';
        $default["pipecntbypatient"] = 0;
        $default["month_pos"] = 0;

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
