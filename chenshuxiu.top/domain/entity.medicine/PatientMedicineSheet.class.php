<?php

/*
 * PatientMedicineSheet
 * 患者核对用药答卷
 */
class PatientMedicineSheet extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  //
            'doctorid',  // doctorid
            'thedate',  // thedate
            'markcnt',  // 标记需审核的明细数目,sum(ismark)
            'status',  // 状态
            'content',  // 患者提交备注
            'createby',  // 创建者
            'auditstatus',  // 审核状态 0,待审核;1,审核正确;2,审核错误
            'auditorid',  // auditorid
            'auditremark',  // 审核备注
            'audittime'); // 审核通过时间
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
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["thedate"] = $thedate;
    // $row["markcnt"] = $markcnt;
    // $row["status"] = $status;
    // $row["auditstatus"] = $auditstatus;
    // $row["auditorid"] = $auditorid;
    // $row["auditremark"] = $auditremark;
    // $row["audittime"] = $audittime;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientMedicineSheet::createByBiz row cannot empty");

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
        $default["thedate"] = date('Y-m-d');
        $default["markcnt"] = 0;
        $default["status"] = 0;
        $default["content"] = '';
        $default["createby"] = '';
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';
        $default["audittime"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function auditRight ($auditorid) {
        $this->auditstatus = 1;
        $this->auditorid = $auditorid;
        $this->audittime = date("Y-m-d H:i:s");

        $pcard = $this->patient->getMasterPcard();
        $pcard->send_pmsheet_status = 0;

        #5293 关闭该个用药核对审核任务
        $optask = OpTaskDao::getOneByObj($this);
        if ($optask instanceof OpTask) {
            OpTaskStatusService::changeStatus($optask, 1, $auditorid);
        }
    }

    public function auditWrong ($auditorid) {
        $this->auditstatus = 2;
        $this->auditorid = $auditorid;
        $this->audittime = date("Y-m-d H:i:s");

        $pcard = $this->patient->getMasterPcard();
        $pcard->send_pmsheet_status = 0;

        #5293 关闭该个用药核对审核任务
        $optask = OpTaskDao::getOneByObj($this);
        if ($optask instanceof OpTask) {
            OpTaskStatusService::changeStatus($optask, 1, $auditorid);
        }
    }

    public function isCreateByAuditor() {
        return $this->createby == 'Auditor';
    }

    //被Pipe.class.php 的getWriter()调用
    public function getWriter() {
        if ($this->createby == 'Auditor') {
            return $this->auditor->name;
        }
        return '';
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
