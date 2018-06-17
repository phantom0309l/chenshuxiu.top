<?php

// RevisitTkt
// 加号单

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class RevisitTkt extends Entity
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
            'revisitrecordid',  // revisitrecordid
            'scheduleid',  // scheduleid
            'thedate',  // 预约日期
            'out_case_no',  // 院内病历号
            'patientcardno',  // 院内就诊卡号
            'patientcard_id',  // 院内患者ID
            'bingan_no',  // 院内病案号
            'treat_stage',  // 手术，（术前，术后）
            'patient_content',  // 患者写的话
            'patient_confirm_status',  // 患者确认状态，0：未确认，1：确认来，2：确认不来
            'createby',  // 创建人: Doctor,Patient, Auditor, System
            'is_mark_his',  // 是否加号
            'status',  // 状态, 0: 无效, 1: 有效
            'isclosed',  // 是否关闭, 如果是取消,同时将status=0; 提交revisit 时, isclosed=1
            'closeby',  // 关闭人: Doctor,Patient, Auditor, System
            'closeremark',  // 关闭原因
            'checkuptplids',  // 开的检查清单,逗号分隔id
            'send_cnt',  // 已发送次数
            'yuyue_platform',  // 预约途径(平台)
            'auditstatus',  // 审核状态, 0: 待审核, 1: 审核通过, 2: 审核拒绝, 3: 运营下线
            'auditorid',  // auditorid
            'audittime',  // 审核时间
            'auditremark'); // 审核备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'revisitrecordid',
            'scheduleid',  // set4lock 修改之
            'auditorid'); // set4lock 修改之
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

        $this->_belongtos["revisitrecord"] = array(
            "type" => "RevisitRecord",
            "key" => "revisitrecordid");

        $this->_belongtos["schedule"] = array(
            "type" => "Schedule",
            "key" => "scheduleid");

        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "RevisitTkt::createByBiz row cannot empty");

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
        $default["revisitrecordid"] = 0;
        $default["scheduleid"] = 0;
        $default["thedate"] = '0000-00-00';
        $default["out_case_no"] = '';
        $default["patientcardno"] = '';
        $default["patientcard_id"] = '';
        $default["bingan_no"] = '';
        $default["treat_stage"] = '';
        $default["patient_content"] = '';
        $default["patient_confirm_status"] = 0;
        $default["createby"] = '';
        $default["is_mark_his"] = 0;
        $default["status"] = 1;
        $default["isclosed"] = 0;
        $default["closeby"] = '';
        $default["closeremark"] = '';
        $default["checkuptplids"] = '';
        $default["send_cnt"] = 0;
        $default["yuyue_platform"] = 'fangcun';
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["audittime"] = '0000-00-00 00:00:00';
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 创建人
    public function getCreatebyStr () {
        $arr = self::getCreatebyArray();
        return $arr[$this->createby];
    }

    // 预约平台
    public function getYuyue_platformDesc () {
        $yuyue_platform = $this->yuyue_platform;
        if ($yuyue_platform === '') {
            return '方寸医生';
        }

        $arr = [
            'fangcun' => '方寸医生',
            'xieheapp' => '协和app',
            '114' => '114平台'];
        return $arr[$yuyue_platform];
    }

    // 通过
    public function pass () {
        $this->auditstatus = 1;
        $this->auditremark = '';
        $this->audittime = date('Y-m-d H:i:s');
        $this->status = 1;
        $this->isclosed = 0;
    }

    // 拒绝
    public function refuse ($auditremark = '') {
        $this->auditstatus = 2;
        $this->auditremark = $auditremark;
        $this->audittime = date('Y-m-d H:i:s');
        $this->status = 0;
        $this->isclosed = 1;
    }

    // 运营下线
    public function auditOffline ($auditremark = '') {
        $this->auditstatus = 3;
        $this->auditremark = $auditremark;
        $this->audittime = date('Y-m-d H:i:s');
        $this->closeby = 'Auditor';
        $this->status = 0;
        $this->isclosed = 1;
    }

    // getIsclosedStr
    public function getIsclosedStr () {
        if ($this->isclosed) {
            return '关闭';
        } else {
            return '未关闭';
        }
    }

    // TODO rework 调用的函数名莫名其妙
    public function getFormatThedate () {
        return $this->schedule->getDescStr4Revisittkt();
    }

    // TODO rework 参考 XConst
    public function getDescStep () {
        if ($this->auditstatus == 1) {
            return "已通过";
        }
        if ($this->auditstatus == 0) {
            return "审核中";
        }
        return "未通过";
    }

    // 判断可以修改或取消患者日期 TODO rework 改注释
    public function isCanModifyOrCancelPatientThedate () {
        $thedate = $this->thedate;
        $now_time = time();
        $thedate_before2days = strtotime($thedate) - 2 * 24 * 3600;
        if ($thedate_before2days <= $now_time) {
            return false;
        }

        return true;
    }

    // 判断过期 TODO rework 改注释
    public function isPatientThedateExpire () {
        $thedate = $this->thedate;
        $now_time = time();
        $thedate_after12hours = strtotime($thedate) + 6 * 3600;
        if ($thedate_after12hours <= $now_time) {
            return true;
        }

        return false;
    }

    public function getCheckupTpls () {
        $checkuptplids = $this->checkuptplids;
        if ($checkuptplids == '') {
            return array();
        }
        $checkuptplidsArr = explode(',', $checkuptplids);

        $checkuptpls = array();
        foreach ($checkuptplidsArr as $checkuptplid) {
            if (empty($checkuptplid)) {
                continue;
            }

            $checkuptpl = CheckupTpl::getById($checkuptplid);
            if (false == $checkuptpl instanceof CheckupTpl) {
                continue;
            }

            $checkuptpls[] = $checkuptpl;
        }

        return $checkuptpls;
    }

    public function saveCheckupTplids (array $checkupTplids) {
        $idsArr = array();
        foreach ($checkupTplids as $checkuptplid) {
            if (empty($checkuptplid)) {
                continue;
            }

            $checkuptpl = CheckupTpl::getById($checkuptplid);
            if (false == $checkuptpl instanceof CheckupTpl) {
                continue;
            }
            $idsArr[] = $checkuptplid;
        }

        $this->checkuptplids = implode(',', $idsArr);
    }

    public function getCheckuptplTitles () {
        $checkuptpls = $this->getCheckupTpls();

        $arr = array();
        foreach ($checkuptpls as $checkuptpl) {
            $arr[] = $checkuptpl->title;
        }

        return $arr;
    }

    public function getDescArr () {
        $patient_confirm_status = $this->patient_confirm_status;
        $status = $this->status;
        $isclosed = $this->isclosed;
        $send_cnt = $this->send_cnt;
        $auditstatus = $this->auditstatus;

        if ($auditstatus == 3) {
            return [
                "运营下线",
                '#f06602'];
        }

        if ($auditstatus == 2) {
            return [
                "审核拒绝",
                '#f06602'];
        }

        if ($auditstatus == 0) {
            return [
                "待审核",
                '#37b031'];
        }

        if ($status == 0 && $isclosed == 1) {
            if ($this->closeby == 'Patient') {
                return [
                    "患者取消",
                    '#f06602'];
            } elseif ($this->closeby == 'Auditor') {
                return [
                    "运营下线",
                    '#f06602'];
            }
            return [
                "取消",
                '#f06602'];
        }

        if ($status == 1 && $isclosed == 1) {
            return [
                "已完成",
                '#37b031'];
        }

        if ($send_cnt <= 1) {
            if ($this->createby == "Patient") {
                return [
                    "审核通过",
                    '#37b031'];
            } else {
                return [
                    "审核通过",
                    '#37b031'];
            }
        }

        if ($send_cnt == 2) {
            if ($patient_confirm_status == 0) {
                return [
                    "未确认",
                    '#37b031'];
            }

            if ($patient_confirm_status == 1) {
                return [
                    "患者确认",
                    '#37b031'];
            }

            if ($patient_confirm_status == 2) {
                return [
                    "患者拒绝",
                    '#f06602'];
            }
        }

        if ($this->thedate >= date('Y-m-d')) {
            return [
                "进行中",
                '#37b031'];
        }

        return [
            "过期",
            '#ddd'];
    }

    public function getPatient_confirm_statusStr () {
        if ($this->patient_confirm_status == 0) {
            return "<span style='color:#ccc'>未确认</span>";
        }

        if ($this->patient_confirm_status == 1) {
            return "<span style='color:#37b031'>患者确认</span>";
        }

        if ($this->patient_confirm_status == 2) {
            return "<span style='color:#f06602'>患者拒绝</span>";
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 创建人
    public static function getCreatebyArray () {
        $arr = array(
            'Doctor' => '医生',
            'Patient' => '患者',
            'Auditor' => '医助',
            'System' => '医助');
        return $arr;
    }
}
