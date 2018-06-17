<?php

/*
 * Rpt_patient
 */
class Rpt_patient extends Entity
{

    protected function init_database () {
        $this->database = 'statdb';
    }

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'patientid',  // patientid
            'doctorid',  // doctorid
            'isscan',  // 是否扫码 0：非扫码；1：扫码
            'thedate',  // 日期
            'patient_status',  // 患者状态
            'isbaodao',  // 已报到
            'baodaodate',  // 报到时间
            'patient_daycnt_lifecycle',  // 天数 = 取消关注日期—报到日期天数 =
                                        // 取消关注日期—报到日期，活跃患者此处为-1
            'medicinestr',  // 当时的服药情况
            'isactivity',  // 当时的有效状态
            'drug_status',  // 用药状态,0:无填写记录，1：用药，2：不服药，3：停药
            'drugitem_cnt',  // 用药记录数
            'paper_cnt',  // 填写量表数
            'pipe_cnt',  // 当日总流数
            'wxpicmsg_cnt',  // 图片消息数
            'wxvoicemsg_cnt',  // 语音消息数
            'wxtxtmsg_cnt',  // 文本消息数
            'answersheet_cnt',  // 答卷数
            'patientnote_cnt',  // 日记数
            'fbt_cnt',  // 课程作业数
            'lastactivitydate',  // 上次活跃日期
            'nextactivitydate',  // 下次活跃日期
            'lxgc_all',  // 查看疗效评估数
            'lxgc_test_cnt',  // 做巩固数
            'lxgc_hwk_cnt',  // 做作业数
            'lessonuserref_hwk_cnt',  // 课文作业数
            'lessonuserref_test_cnt',  // 课文巩固数
            'comment_share_cnt'); // 分享数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_patient::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["isscan"] = 0;
        $default["thedate"] = '0000-00-00';
        $default["patient_status"] = 0;
        $default["isbaodao"] = 0;
        $default["baodaodate"] = '0000-00-00';
        $default["patient_daycnt_lifecycle"] = 0;
        $default["medicinestr"] = '';
        $default["isactivity"] = 0;
        $default["drug_status"] = 0;
        $default["drugitem_cnt"] = 0;
        $default["paper_cnt"] = 0;
        $default["pipe_cnt"] = 0;
        $default["wxpicmsg_cnt"] = 0;
        $default["wxvoicemsg_cnt"] = 0;
        $default["wxtxtmsg_cnt"] = 0;
        $default["answersheet_cnt"] = 0;
        $default["medicineorder_cnt"] = 0;
        $default["patientnote_cnt"] = 0;
        $default["fbt_cnt"] = 0;
        $default["lastactivitydate"] = '0000-00-00';
        $default["nextactivitydate"] = '0000-00-00';
        $default["lxgc_all"] = 0;
        $default["lxgc_test_cnt"] = 0;
        $default["lxgc_hwk_cnt"] = 0;
        $default["lessonuserref_hwk_cnt"] = 0;
        $default["lessonuserref_test_cnt"] = 0;
        $default["comment_share_cnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getActivityStr () {
        if ($this->isactivity) {
            return "活跃";
        }

        return "";
    }

    public function getActivityStrFix ($beforeOne) {
        $class = 'blue';

        if ($this->isactivity) {
            $str = "新活";
        }

        if ($beforeOne instanceof Rpt_patient) {
            if ($beforeOne->isactivity && $this->isactivity) {
                $str = "存活";
                $class = 'gray';
            } elseif (false == $beforeOne->isactivity && $this->isactivity) {
                $str = "复活";
                $class = 'red';
            } elseif ($beforeOne->isactivity && false == $this->isactivity) {
                $str = "失活";
                $class = 'red';
            }
        }

        return $str = "<span class='{$class}'>{$str}</span>";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
