<?php

/*
 * Rpt_patient_month
 */
class Rpt_patient_month extends Entity
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
            'doctorid',  // doctorid
            'isscan',  // 是否扫码，0：非扫码；1：扫码
            'baodaodate',  // baodaodate
            'patient_daycnt_lifecycle',  // 天数 = 取消关注日期—报到日期，活跃患者此处为-1
            'themonth',  // 年-月
            'month_offsetcnt',  // 月数 = 当前月份—报到月份 + 1
            'patient_status_first',  // 当月第一天患者状态
            'patient_status_last',  // 当月最后一天患者状态
            'patient_pipe_cnt',  // 患者活跃流汇总数
            'drugitem_cnt',  // 用药记录数
            'drug_status_first',  // 当月第一天患者用药状态,0:无填写记录，1：用药，2：不服药，3：停药
            'drug_status_last') // 当月最后一天患者用药状态,0:无填写记录，1：用药，2：不服药，3：停药
;
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'doctorid');
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

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["isscan"] = $isscan;
    // $row["baodaodate"] = $baodaodate;
    // $row["patient_daycnt_lifecycle"] = $patient_daycnt_lifecycle;
    // $row["themonth"] = $themonth;
    // $row["month_offsetcnt"] = $month_offsetcnt;
    // $row["patient_status_first"] = $patient_status_first;
    // $row["patient_status_last"] = $patient_status_last;
    // $row["patient_pipe_cnt"] = $patient_pipe_cnt;
    // $row["drugitem_cnt"] = $drugitem_cnt;
    // $row["drug_status_first"] = $drug_status_first;
    // $row["drug_status_last"] = $drug_status_last;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_patient_month::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["isscan"] = 0;
        $default["baodaodate"] = '';
        $default["patient_daycnt_lifecycle"] = 0;
        $default["themonth"] = '';
        $default["month_offsetcnt"] = 0;
        $default["patient_status_first"] = 0;
        $default["patient_status_last"] = 0;
        $default["patient_pipe_cnt"] = 0;
        $default["drugitem_cnt"] = 0;
        $default["drug_status_first"] = 0;
        $default["drug_status_last"] = 0;

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
