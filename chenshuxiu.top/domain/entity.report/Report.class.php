<?php

/*
 * Report
 */
class Report extends Entity
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
            'appeal',  // 患者诉求
            'remark',  // 运营备注
            'data_json',  // 数据JSON
            'issend'); // 是否发送
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
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["appeal"] = $appeal;
    // $row["remark"] = $remark;
    // $row["data_json"] = $data_json;
    // $row["issend"] = $issend;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Report::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["appeal"] = '';
        $default["remark"] = '';
        $default["data_json"] = '';
        $default["issend"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getReportPictures () {
        $reportpictures = ReportPictureDao::getListByReportid($this->id);
        return $reportpictures ? $reportpictures : [];
    }

    public function getDoctorComments() {
        $doctorComments = DoctorCommentDao::getListByObjtypeAndObjid('Report', $this->id);
        return $doctorComments;
    }

    public function isReply() {
        $doctorComment = DoctorCommentDao::getByObjtypeAndObjid('Report', $this->id);
        return $doctorComment instanceof DoctorComment ? 1 : 0;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
