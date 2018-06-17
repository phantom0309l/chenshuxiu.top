<?php

/*
 * Rpt_date_wxuser
 */
class Rpt_date_wxuser extends Entity
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
            'thedate',  // 日期
            'allcnt',  // 总人数
            'pipe_sumcnt',  // 当日流,总数
            'wxpicmsg_sumcnt',  // 图片消息,总数
            'wxtxtmsg_sumcnt',  // 文本消息,总数
            'answersheet_sumcnt',  // 答卷,总数
            'patientnote_sumcnt',  // 日记,总数
            'fbt_sumcnt',  // 培训课作业,总数
            'pipe_pcnt',  // 有流,人数
            'wxpicmsg_pcnt',  // 有图片消息,人数
            'wxtxtmsg_pcnt',  // 有文本消息,人数
            'answersheet_pcnt',  // 有答卷,人数
            'patientnote_pcnt',  // 有日记,人数
            'fbt_pcnt',  // 有培训课,人数
            'lxgc_sumall',  // 查看疗效评估总数
            'lxgc_test_sumcnt',  // 做巩固总数
            'lxgc_hwk_sumcnt',  // 做作业总数
            'lxgc_pall',  // 查看疗效评估, 人数
            'lxgc_test_pcnt',  // 做巩固, 人数
            'lxgc_hwk_pcnt'); // 做作业, 人数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_date_WxUser::createByBiz row cannot empty");

        $default = array();
        $default["thedate"] = '';
        $default["allcnt"] = 0;
        $default["pipe_sumcnt"] = 0;
        $default["wxpicmsg_sumcnt"] = 0;
        $default["wxtxtmsg_sumcnt"] = 0;
        $default["answersheet_sumcnt"] = 0;
        $default["patientnote_sumcnt"] = 0;
        $default["fbt_sumcnt"] = 0;
        $default["pipe_pcnt"] = 0;
        $default["wxpicmsg_pcnt"] = 0;
        $default["wxtxtmsg_pcnt"] = 0;
        $default["answersheet_pcnt"] = 0;
        $default["patientnote_pcnt"] = 0;
        $default["fbt_pcnt"] = 0;
        $default["lxgc_sumall"] = 0;
        $default["lxgc_test_sumcnt"] = 0;
        $default["lxgc_hwk_sumcnt"] = 0;
        $default["lxgc_pall"] = 0;
        $default["lxgc_test_pcnt"] = 0;
        $default["lxgc_hwk_pcnt"] = 0;

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
