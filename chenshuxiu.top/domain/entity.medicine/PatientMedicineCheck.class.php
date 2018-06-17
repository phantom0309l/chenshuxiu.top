<?php

/*
 * PatientMedicineCheck
 */

class PatientMedicineCheck extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'type',  // 类型[targeted_drug: 靶向药, multiple_diseases: 多疾病]
            'plan_send_date',  // 计划发送日期
            'submit_time',  // 提交时间
            'content',  // 数据JSON
            'status' // 0未发送，1已发送，2已填写, 3关闭
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");

        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["type"] = $type;
    // $row["submit_time"] = $submit_time;
    // $row["content"] = $content;
    // $row["plan_send_date"] = $plan_send_date;
    // $row["status"] = $status;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "PatientMedicineCheck::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["type"] = '';
        $default["submit_time"] = '0000-00-00 00:00:00';
        $default["content"] = '';
        $default["plan_send_date"] = date("Y-m-d", strtotime("+28 day", time()));
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeStr() {
        return self::getTypes()[$this->type];
    }

    public function getStatusStr() {
        $status_arr = [
            0 => '未发送',
            1 => '已发送',
            2 => '已填写'];
        return $status_arr[$this->status];
    }

    public function getContentData() {
        return json_decode($this->content);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getTypes() {
        return [
            'targeted_drug' => '靶向药',
            'multiple_diseases' => '多疾病'];
    }
}
