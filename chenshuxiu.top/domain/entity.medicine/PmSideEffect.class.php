<?php

/*
 * PmSideEffect
 */
class PmSideEffect extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'medicineid',  // medicineid
            'thedate',  // 副反应反馈日期
            'result_status',  // 结果 0,没有结果；1,资料正确；2,资料不正确；3,没有上传
            'content'); // 具体内容
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid');
    }

    protected function init_belongtos () {
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
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["medicineid"] = $medicineid;
    // $row["thedate"] = $thedate;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PmSideEffect::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["medicineid"] = 0;
        $default["thedate"] = '';
        $default["content"] = '';
        $default["result_status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getResultDesc () {
        $arr = array(
            '0' => '结果待处理',
            '1' => '资料正确',
            '2' => '资料不正确',
            '3' => '没有上传');

        return $arr[$this->result_status];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getPMMedicines () {
        $medicines = array();

        // 赛可平
        $medicines[4] = Medicine::getById(4);
        // 骁悉
        $medicines[11] = Medicine::getById(11);
        // 依木兰 硫唑嘌呤
        $medicines[5] = Medicine::getById(5);
        // 普乐可复 他克莫司
        $medicines[72] = Medicine::getById(72);
        // 他克莫司 他克莫司
        $medicines[73] = Medicine::getById(73);
        // 异力抗 他克莫司
        $medicines[74] = Medicine::getById(74);
        // 硫唑嘌呤 硫唑嘌呤
        $medicines[20] = Medicine::getById(20);

        return $medicines;
    }
}
