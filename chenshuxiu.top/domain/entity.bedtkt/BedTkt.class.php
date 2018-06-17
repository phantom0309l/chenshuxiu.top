<?php

/*
 * BedTkt
 */

class BedTkt extends Entity
{

    // 草稿
    const DRAFT_STATUS = 0;
    // 待运营审核
    const WILL_AUDITOR_STATUS = 1;
    // 患者取消
    const PATIENT_CANCEL_STATUS = 2;
    // 运营通过
    const AUDITOR_PASS_STATUS = 3;
    // 运营拒绝
    const AUDITOR_REFUSE_STATUS = 4;
    // 医生通过
    const DOCTOR_PASS_STATUS = 5;
    // 医生拒绝
    const DOCTOR_REFUSE_STATUS = 6;

    //医生审核住院资料初始状态(未审核)
    const DOCTOR_AUDIT_APPLY_STATUS = 0;
    //医生审核住院资料通过
    const DOCTOR_AUDIT_PASS_STATUS = 1;
    //医生审核住院资料拒绝
    const DOCTOR_AUDIT_REFUSE_STATUS = 2;

    const TYPESTR_STATUS = array(
        self::DRAFT_STATUS => '草稿',
        self::WILL_AUDITOR_STATUS => '待运营审核',
        self::PATIENT_CANCEL_STATUS => '患者取消',
        self::AUDITOR_PASS_STATUS => '运营通过',
        self::AUDITOR_REFUSE_STATUS => '运营拒绝',
        self::DOCTOR_PASS_STATUS => '医生通过',
        self::DOCTOR_REFUSE_STATUS => '医生拒绝');

    // 待发送
    const NEED_SEND_PATIENT_STATUS = 0;
    // 已发送待确认
    const NEED_CONFIRM_PATIENT_STATUS = 1;
    // 患者确认来
    const CONFIRM_YES_PATIENT_STATUS = 2;
    // 患者确认拒绝
    const CONFIRM_NO_PATIENT_STATUS = 3;
    const TYPESTR_PATIENT_STATUS = array(
        self::NEED_SEND_PATIENT_STATUS => '待发送患者确认',
        self::NEED_CONFIRM_PATIENT_STATUS => '已发送待患者确认',
        self::CONFIRM_YES_PATIENT_STATUS => '患者确认来',
        self::CONFIRM_NO_PATIENT_STATUS => '患者确认拒绝');

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'typestr',  // 约床位类型 治疗 treat 检查 checkup
            'fee_type',  // 费用类型
            'want_date',  // 希望入住日期
            'plan_date',  // 应住日期
            'confirm_date',  // 最终确认时医患约定日期
            'submit_time',  // 提交时间
            'notify_time',  // 通知时间;医生审核时间
            'audit_time',  // 运营审核时间
            'doctor_audit_time', // 医生审核时间
            'doctor_audit_status', // 医生审核状态
            'status',  // 状态 0:草稿 1:待运营审核 ........
            'status_by_patient',  // 患者最后确认流程 0: 待发送 1：已发送待确认 2：患者确认来 3:患者确认拒绝
            'auditor_remark',  // 运营备注
            'doctor_remark',  // 医生备注
            'patient_remark', // 患者备注
            'extra_info'); // 其他信息，可以存储任何内容
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid');
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
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["fee_type"] = $fee_type;
    // $row["want_date"] = $want_date;
    // $row["submit_time"] = $submit_time;
    // $row["notify_time"] = $notify_time;
    // $row["status"] = $status;
    // $row["auditor_remark"] = $auditor_remark;
    // $row["doctor_remark"] = $doctor_remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "BedTkt::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["typestr"] = '';
        $default["fee_type"] = 'default';
        $default["want_date"] = '';
        $default["plan_date"] = '';
        $default["confirm_date"] = '';
        $default["submit_time"] = '';
        $default["notify_time"] = '';
        $default["audit_time"] = '';
        $default["doctor_audit_time"] = '';
        $default["doctor_audit_status"] = 0;
        $default["status"] = 0;
        $default["status_by_patient"] = 0;
        $default["auditor_remark"] = '';
        $default["doctor_remark"] = '';
        $default["patient_remark"] = '';
        $default["extra_info"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 设置为待运营审核状态
    public function setWillAuditorStatus() {
        BedTktStatusService::setBedTktStatus($this, self::WILL_AUDITOR_STATUS);
    }

    // 设置为患者取消状态
    public function setPatientCancelStatus() {
        BedTktStatusService::setBedTktStatus($this, self::PATIENT_CANCEL_STATUS);
    }

    // 设置为运营通过状态
    public function setAuditorPassStatus() {
        BedTktStatusService::setBedTktStatus($this, self::AUDITOR_PASS_STATUS);
    }

    // 设置为运营拒绝状态
    public function setAuditorRefuseStatus() {
        BedTktStatusService::setBedTktStatus($this, self::AUDITOR_REFUSE_STATUS);
    }

    // 设置为医生通过状态
    public function setDoctorPassStatus() {
        BedTktStatusService::setBedTktStatus($this, self::DOCTOR_PASS_STATUS);
    }

    // 设置为医生拒绝状态
    public function setDoctorRefuseStatus() {
        BedTktStatusService::setBedTktStatus($this, self::DOCTOR_REFUSE_STATUS);
    }

    // 设置为已发送待确认
    public function setNeedConfirmPatientStatus() {
        BedTktStatusService::setBedTktPatientStatus($this, self::NEED_CONFIRM_PATIENT_STATUS);
    }

    // 设置为患者确认来
    public function setConfirmYesPatientStatus() {
        BedTktStatusService::setBedTktPatientStatus($this, self::CONFIRM_YES_PATIENT_STATUS);
    }

    // 设置为患者确认拒绝
    public function setConfirmNoPatientStatus() {
        BedTktStatusService::setBedTktPatientStatus($this, self::CONFIRM_NO_PATIENT_STATUS);
    }

    public function getStatusDesc($status = '') {
        if (!$status) {
            $status = $this->status;
        }
        return self::TYPESTR_STATUS[$status];
    }

    public function getPatientStatusDesc() {
        $desc = '';
        $patientstatus = $this->status_by_patient;
        switch ($patientstatus) {
            case self::NEED_SEND_PATIENT_STATUS:
                $desc = '未发送确认消息';
                break;
            case self::NEED_CONFIRM_PATIENT_STATUS:
                $desc = '已询问患者' . "[{$this->confirm_date}入院]";
                break;
            case self::CONFIRM_YES_PATIENT_STATUS:
                $desc = '患者同意入院' . "[{$this->confirm_date}入院]";
                break;
            case self::CONFIRM_NO_PATIENT_STATUS:
                $desc = '患者拒绝入院';
                break;
            default:
                break;
        }

        return $desc;
    }

    public function getBedTktPictures() {
        return BedTktPictureDao::getListByBedTkt($this);
    }

    public function getLiverPictures() {
        return LiverPictureDao::getListByObj($this);
    }

    public function getWxPicMsgs() {
        return WxPicMsgDao::getListByObj($this);
    }

    //心电图
    public function getXindiantuPictures() {
        return BasicPictureDao::getListByObjtypeObjidAndType('BedTkt', $this->id, 'xindiantu');
    }

    //血栓弹力图
    public function getXueshuantanlituPictures() {
        return BasicPictureDao::getListByObjtypeObjidAndType('BedTkt', $this->id, 'xueshuantanlitu');
    }

    //风湿免疫检查
    public function getFengshimianyijianchaPictures() {
        return BasicPictureDao::getListByObjtypeObjidAndType('BedTkt', $this->id, 'fengshimianyijiancha');
    }

    //术前其他检查
    public function getShuqianqitajianchaPictures() {
        return BasicPictureDao::getListByObjtypeObjidAndType('BedTkt', $this->id, 'shuqianqitajiancha');
    }

    public function getFee_TypeDesc() {
        $arr = array(
            'default' => '未选择',
            'beijing' => '北京',
            'notbeijing' => '非北京');

        return $arr["{$this->fee_type}"];
    }

    public function saveLog($type, $content, $auditorid = 0) {
        $row = array();
        $row['auditorid'] = $auditorid;
        $row['bedtktid'] = $this->id;
        $row['bedtkt_status'] = $this->status;
        $row['type'] = $type;
        $row['content'] = $content;

        return BedTktLog::createByBiz($row);
    }

    public function getLogs() {
        return BedTktLogDao::getListByBedTkt($this, " AND  type<>'status_change' AND type<>'' ");
    }

    public function getLastLog() {
        $logs = $this->getLogs();
        return array_pop($logs);
    }

    public function getTypestrDesc() {
        if ($this->typestr === '') {
            return '住院治疗';
        }

        $arr = [
            'treat' => '住院治疗',
            'checkup' => '住院检查'];

        return $arr[$this->typestr];
    }

    public function send2PatientByRefuse (BedTkt $bedtkt, $auditorid) {
        $wxuser = $bedtkt->wxuser;
        $myauditor = Auditor::getById($auditorid);

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $config_content = json_decode($bedtktconfig->content, true);

        if ($config_content['is_auditrefuse_notice_open'] == 1) {
            // 发通知
            $first = array(
                "value" => "住院申请被拒绝",
                "color" => "");
            $keyword2 = $config_content['auditrefuse_notice_content'] ? $config_content['auditrefuse_notice_content'] : "您的住院申请未通过，如有问题请与我们联系";

            $keywords = array(
                array(
                    "value" => "{$bedtkt->doctor->name}",
                    "color" => "#ff6600"
                        ),
                        array(
                            "value" => $keyword2,
                            "color" => "#ff6600"
                        )
                        );
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, "doctornotice", $content);
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getOpenStatusStr() {
        return self::WILL_AUDITOR_STATUS . "," . self::AUDITOR_PASS_STATUS;
    }

    public static function getCloseStatusStr() {
        return self::PATIENT_CANCEL_STATUS . "," . self::AUDITOR_REFUSE_STATUS . "," . self::DOCTOR_REFUSE_STATUS . "," . self::DOCTOR_PASS_STATUS;
    }
}
