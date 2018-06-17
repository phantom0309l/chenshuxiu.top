<?php

/*
 * Rpt_doctor_month
 */
class Rpt_doctor_month extends Entity
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
            'themonth',  // 年-月
            'month_offsetcnt',  // 月数 = 当前月-第一个有效患者报到月份 + 1
            'patient_cnt_all',  // 截止到当月底累计患者数 = sum(patient_cnt_baodao)
            'patient_cnt_all_scan',  // 截止到当月底累计扫码患者数 =
                                    // sum(patient_cnt_baodao_isscan)
            'patient_cnt_all_active',  // 全部患者,当月活跃的人数
            'patient_cnt_all_active_scan',  // 全部扫码患者,当月活跃人数
            'patient_cnt_all_drug',  // 全部患者,当月服药人数(月末当天状态)
            'patient_cnt_all_drug_scan',  // 全部扫码患者,当月服药人数(月末当天状态)
            'patient_cnt_all_drugitem',  // 全部患者,当月有服药记录的人数
            'patient_cnt_all_drugitem_scan',  // 全部扫码患者,当月有服药记录的人数
            'patient_cnt_baodao',  // 当月报到人数
            'patient_cnt_baodao_scan',  // 当月扫码报到人数
            'patient_cnt_baodao_drug',  // 当月报到的人的用药人数
            'patient_cnt_baodao_drug_scan',  // 当月扫码报到的人的用药人数
            'wxuser_cnt_all',  // 全部关注数
            'wxuser_cnt_all_unsubscribe',  // 全部取消关注数
            'wxuser_cnt_all_scan',  // 全部扫码关注数
            'wxuser_cnt_all_scan_unsubscribe',  // 全部扫码关注,取消关注数
            'wxuser_cnt',  // 当月关注数
            'wxuser_cnt_scan',  // 当月扫码关注数
            'wxuser_cnt_unsubscribe') // 当月取消关注数
;
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
    // $row["themonth"] = $themonth;
    // $row["month_offsetcnt"] = $month_offsetcnt;
    // $row["patient_cnt_all"] = $patient_cnt_all;
    // $row["patient_cnt_all_scan"] = $patient_cnt_all_scan;
    // $row["patient_cnt_all_active"] = $patient_cnt_all_active;
    // $row["patient_cnt_all_active_scan"] = $patient_cnt_all_active_scan;
    // $row["patient_cnt_all_drug"] = $patient_cnt_all_drug;
    // $row["patient_cnt_all_drug_scan"] = $patient_cnt_all_drug_scan;
    // $row["patient_cnt_all_drugitem"] = $patient_cnt_all_drugitem;
    // $row["patient_cnt_all_drugitem_scan"] = $patient_cnt_all_drugitem_scan;
    // $row["patient_cnt_baodao"] = $patient_cnt_baodao;
    // $row["patient_cnt_baodao_scan"] = $patient_cnt_baodao_scan;
    // $row["patient_cnt_baodao_drug"] = $patient_cnt_baodao_drug;
    // $row["patient_cnt_baodao_drug_scan"] = $patient_cnt_baodao_drug_scan;
    // $row["wxuser_cnt_all"] = $wxuser_cnt_all;
    // $row["wxuser_cnt_all_unsubscribe"] = $wxuser_cnt_all_unsubscribe;
    // $row["wxuser_cnt_all_scan"] = $wxuser_cnt_all_scan;
    // $row["wxuser_cnt_all_scan_unsubscribe"] =
    // $wxuser_cnt_all_scan_unsubscribe;
    // $row["wxuser_cnt"] = $wxuser_cnt;
    // $row["wxuser_cnt_scan"] = $wxuser_cnt_scan;
    // $row["wxuser_cnt_unsubscribe"] = $wxuser_cnt_unsubscribe;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_doctor_month::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["themonth"] = '';
        $default["month_offsetcnt"] = 0;
        $default["patient_cnt_all"] = 0;
        $default["patient_cnt_all_scan"] = 0;
        $default["patient_cnt_all_active"] = 0;
        $default["patient_cnt_all_active_scan"] = 0;
        $default["patient_cnt_all_drug"] = 0;
        $default["patient_cnt_all_drug_scan"] = 0;
        $default["patient_cnt_all_drugitem"] = 0;
        $default["patient_cnt_all_drugitem_scan"] = 0;
        $default["patient_cnt_baodao"] = 0;
        $default["patient_cnt_baodao_scan"] = 0;
        $default["patient_cnt_baodao_drug"] = 0;
        $default["patient_cnt_baodao_drug_scan"] = 0;
        $default["wxuser_cnt_all"] = 0;
        $default["wxuser_cnt_all_unsubscribe"] = 0;
        $default["wxuser_cnt_all_scan"] = 0;
        $default["wxuser_cnt_all_scan_unsubscribe"] = 0;
        $default["wxuser_cnt"] = 0;
        $default["wxuser_cnt_scan"] = 0;
        $default["wxuser_cnt_unsubscribe"] = 0;

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
