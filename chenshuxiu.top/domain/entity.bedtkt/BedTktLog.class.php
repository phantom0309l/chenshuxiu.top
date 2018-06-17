<?php

/*
 * BedTktLog
 */
class BedTktLog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'auditorid',    //auditorid
            'bedtktid',  // bedtktid
            'bedtkt_status',  // 记录日志时状态
            'type',  // 日志类型
            'content'); // 日志内容
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'bedtktid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["bedtkt"] = array(
            "type" => "BedTkt",
            "key" => "bedtktid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["bedtktid"] = $bedtktid;
    // $row["bedtkt_status"] = $bedtkt_status;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "BedTktLog::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["bedtktid"] = 0;
        $default["bedtkt_status"] = 0;
        $default["type"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeDesc () {
        $arr = array(
            'status_change' => '状态变化',
            'patient_submit' => '发起申请',
            'patient_cancel' => '患者取消',
            'auditor_pass' => '审核通过',
            'auditor_refuse' => '审核不通过',
            'doctor_confirm' => '询问',
            'patient_pass' => '患者同意入院',
            'patient_refuse' => '患者拒绝入院',
            'doctor_pass' => '医生通过',
            'doctor_refuse' => '医生拒绝');

        return $arr[$this->type];
    }

    public function getBedTktStatusDesc() {
        //trick 方案
        if ($this->type == 'patient_submit') {
           return '申请入院'; 
        }
        return $this->bedtkt->getStatusDesc($this->bedtkt_status);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
