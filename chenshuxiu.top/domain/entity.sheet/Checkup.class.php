<?php
// Checkup
// 检查报告

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class Checkup extends Entity
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
            'checkuptplid',  // checkuptplid
            'xanswersheetid',  // xanswersheetid
            'check_date',  // 检查日期
            'title',  // title
            'hospitalstr',  // 检查的医院
            'content',  // 患者提交的信息
            'status',  // 是否有效
            'auditstatus',  // 审核状态
            'auditremark',  // 审核备注
            'auditorid'); // auditorid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'checkuptplid');
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

        $this->_belongtos["checkuptpl"] = array(
            "type" => "CheckupTpl",
            "key" => "checkuptplid");
        $this->_belongtos["xanswersheet"] = array(
            "type" => "XAnswerSheet",
            "key" => "xanswersheetid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["check_date"] = $check_date;
    // $row["hospitalstr"] = $hospitalstr;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["auditstatus"] = $auditstatus;
    // $row["auditremark"] = $auditremark;
    // $row["auditorid"] = $auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Checkup::createByBiz row cannot empty");

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["checkuptplid"] = 0;
        $default["xanswersheetid"] = 0;
        $default["check_date"] = '0000-00-00';
        $default["title"] = '';
        $default["hospitalstr"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["auditstatus"] = 0;
        $default["auditremark"] = '';
        $default["auditorid"] = 0;

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
