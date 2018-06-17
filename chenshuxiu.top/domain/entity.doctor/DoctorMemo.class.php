<?php

/*
 * DoctorMemo
 */
class DoctorMemo extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'thedate',  // 设定日期
            'content',  // 内容
            'donetime',  // 完成时间
            'status'); // 状态，0表示已关闭，1表示正在进行
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

        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["patientid"] = $patientid;
    // $row["thedate"] = $thedate;
    // $row["content"] = $content;
    // $row["donetime"] = $donetime;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorMemo::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["thedate"] = '';
        $default["content"] = '';
        $default["donetime"] = '';
        $default["status"] = 1;

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
