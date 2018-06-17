<?php

/*
 * Rpt_wxuser
 */
class Rpt_wxuser extends Entity
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
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'thedate',  // 日期
            'isbaodao',  // 已报到
            'medicinestr',  // 当时的服药情况
            'isactivity',  // 当时的有效状态
            'pipe_cnt',  // 当日总流数
            'wxpicmsg_cnt',  // 图片消息数
            'wxtxtmsg_cnt',  // 文本消息数
            'answersheet_cnt',  // 答卷数
            'patientnote_cnt',  // 日记数
            'fbt_cnt',  // 课程作业数
            'lastactivitydate',  // 上次活跃时间
            'nextactivitydate',  // 下次活跃时间
            'lxgc_all',  // 查看疗效观察数
            'lxgc_test_cnt',  // 做课后巩固数
            'lxgc_hwk_cnt'); // 做课后作业数
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

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_WxUser::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["thedate"] = '0000-00-00';
        $default["isbaodao"] = 0;
        $default["medicinestr"] = '';
        $default["isactivity"] = 0;
        $default["pipe_cnt"] = 0;
        $default["wxpicmsg_cnt"] = 0;
        $default["wxtxtmsg_cnt"] = 0;
        $default["answersheet_cnt"] = 0;
        $default["patientnote_cnt"] = 0;
        $default["fbt_cnt"] = 0;
        $default["medicineorder_cnt"] = 0;
        $default["lastactivitydate"] = '0000-00-00';
        $default["nextactivitydate"] = '0000-00-00';
        $default["lxgc_all"] = 0;
        $default["lxgc_test_cnt"] = 0;
        $default["lxgc_hwk_cnt"] = 0;

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
