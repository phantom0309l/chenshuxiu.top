<?php

/*
 * PatientEduRecord
 */

class PatientEduRecord extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'courseid',  // courseid
            'lessonid',  // lessonid
            'first_readtime',  // 首次阅读时间
            'viewcnt'); // 阅读次数
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'lessonid');
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
        $this->_belongtos["lesson"] = array(
            "type" => "Lesson",
            "key" => "lessonid");
        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["courseid"] = $courseid;
    // $row["lessonid"] = $lessonid;
    // $row["first_readtime"] = $first_readtime;
    // $row["viewcnt"] = $viewcnt;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "LessonUserRef::createByBiz row cannot empty");

        if ($row["wxuserid"] == null) {
            $row["wxuserid"] = 0;
        }

        if ($row["userid"] == null) {
            $row["userid"] = 0;
        }

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["courseid"] = 0;
        $default["lessonid"] = 0;
        $default["first_readtime"] = '0000-00-00 00:00:00';
        $default["viewcnt"] = 0;

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
