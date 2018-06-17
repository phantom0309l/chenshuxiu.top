<?php

/*
 * RevisitTktConfig
 */
class RevisitTktConfig extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'isuse_out_case_no',  // 院内病历号, 使用情况 0，不使用；1，使用
            'ismust_out_case_no',  // 院内病历号, 是否必填 0，非必填；1，必填
            'isuse_patientcardno',  // 院内就诊卡号, 使用情况 0，不使用；1，使用
            'ismust_patientcardno',  // 院内就诊卡号, 是否必填 0，非必填；1，必填
            'isuse_patientcard_id',  // 院内患者ID, 使用情况 0，不使用；1，使用
            'ismust_patientcard_id',  // 院内患者ID, 是否必填 0，非必填；1，必填
            'isuse_bingan_no',  // 院内病案号, 使用情况 0，不使用；1，使用
            'ismust_bingan_no',  // 院内病案号, 是否必填 0，非必填；1，必填
            'isuse_treat_stage',  // 治疗阶段字段: 0，不使用；1，使用
            'ismust_treat_stage',  // 治疗阶段字段, 是否必填 0，非必填；1，必填
            'isuse_patient_content',  // 就诊目的, 使用情况 0，不使用；1，使用
            'ismust_patient_content',  // 就诊目的, 是否必填 0，非必填；1，必填
            'remind_status',  // 是否启用复诊提醒
            'remind_pre_day_cnt',  // 复诊提醒 提前提醒通知天数
            'remind_notice',  // 复诊提醒 提前提醒通知内容
            'remind_issend_miss',  // 复诊提醒 是否补发提醒通知
            'confirm_status',  // 是否启用复诊确认
            'confirm_pre_day_cnt',  // 复诊确认 提前确认通知天数
            'confirm_notice',  // 复诊确认 发送确认通知内容
            'confirm_issend_miss',  // 复诊确认 是否补发确认通知
            'confirm_content_yes',  // 复诊确认 确认后结果内容
            'create_optask_not_ontime_status',  // 是否生成未如约复诊跟进任务
            'is_mark_his_notice',  // 是否启用标记加号的推送消息
            'mark_his_notice',  // 标记加号 推送消息内容
            'unmark_his_notice',  // 取消标记加号 推送消息内容
            'status'); // 是否启用整个服务，0：关闭，1：启用
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
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["isuse_out_case_no"] = $isuse_out_case_no;
    // $row["ismust_out_case_no"] = $ismust_out_case_no;
    // $row["isuse_patientcardno"] = $isuse_patientcardno;
    // $row["ismust_patientcardno"] = $ismust_patientcardno;
    // $row["isuse_patientcard_id"] = $isuse_patientcard_id;
    // $row["ismust_patientcard_id"] = $ismust_patientcard_id;
    // $row["isuse_bingan_no"] = $isuse_bingan_no;
    // $row["ismust_bingan_no"] = $ismust_bingan_no;
    // $row["isuse_treat_stage"] = $isuse_treat_stage;
    // $row["ismust_treat_stage"] = $ismust_treat_stage;
    // $row["isuse_patient_content"] = $isuse_patient_content;
    // $row["ismust_patient_content"] = $ismust_patient_content;
    // $row["remind_status"] = $remind_status;
    // $row["remind_pre_day_cnt"] = $remind_pre_day_cnt;
    // $row["remind_notice"] = $remind_notice;
    // $row["remind_issend_miss"] = $remind_issend_miss;
    // $row["confirm_status"] = $confirm_status;
    // $row["confirm_pre_day_cnt"] = $confirm_pre_day_cnt;
    // $row["confirm_notice"] = $confirm_notice;
    // $row["confirm_issend_miss"] = $confirm_issend_miss;
    // $row["confirm_content_yes"] = $confirm_content_yes;
    // $row["is_mark_his_notice"] = $is_mark_his_notice;
    // $row["mark_his_notice"] = $mark_his_notice;
    // $row["unmark_his_notice"] = $mark_his_notice;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "RevisitTktConfig::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["isuse_out_case_no"] = 0;
        $default["ismust_out_case_no"] = 0;
        $default["isuse_patientcardno"] = 0;
        $default["ismust_patientcardno"] = 0;
        $default["isuse_patientcard_id"] = 0;
        $default["ismust_patientcard_id"] = 0;
        $default["isuse_bingan_no"] = 0;
        $default["ismust_bingan_no"] = 0;
        $default["isuse_treat_stage"] = 0;
        $default["ismust_treat_stage"] = 0;
        $default["isuse_patient_content"] = 0;
        $default["ismust_patient_content"] = 0;
        $default["remind_status"] = 0;
        $default["remind_pre_day_cnt"] = 7;
        $default["remind_notice"] = '';
        $default["remind_issend_miss"] = 0;
        $default["confirm_status"] = 0;
        $default["confirm_pre_day_cnt"] = 3;
        $default["confirm_notice"] = '';
        $default["confirm_issend_miss"] = 0;
        $default["confirm_content_yes"] = '';
        $default["create_optask_not_ontime_status"] = 0;
        $default["is_mark_his_notice"] = 0;
        $default["mark_his_notice"] = '';
        $default["unmark_his_notice"] = '';
        $default["status"] = 0;

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
