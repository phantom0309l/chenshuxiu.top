<?php

/*
 * Rpt_date_patient
 */
class Rpt_date_patient extends Entity
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
            'sumcnt0',  // 总报到人数
            'yes_sumcnt0',  // 服药人数,总
            'yes_zsd_cnt0',  // 服择思达的人数,总
            'yes_zzd_cnt0',  // 服专注达的人数,总
            'sumcnt1',  // 人数,活跃
            'yes_sumcnt1',  // 服药人数,活跃
            'yes_zsd_cnt1',  // 服择思达的人数,活跃
            'yes_zzd_cnt1',  // 服专注达的人数,活跃
            'pipe_sumcnt',  // 当日流,总数
            'wxpicmsg_sumcnt',  // 图片消息,总数
            'wxtxtmsg_sumcnt',  // 文本消息,总数
            'answersheet_sumcnt',  // 答卷,总数
            'patientnote_sumcnt',  // 日记,总数
            'fbt_sumcnt',  // 课程作业,总数
            'pipe_pcnt',  // 有流,人数
            'wxpicmsg_pcnt',  // 有图片消息,人数
            'wxtxtmsg_pcnt',  // 有文本消息,人数
            'answersheet_pcnt',  // 有答卷,人数
            'patientnote_pcnt',  // 有日记,人数
            'fbt_pcnt',  // 有课程作业,人数
            'lxgc_sumall',  // 查看疗效评估总数
            'lxgc_test_sumcnt',  // 做巩固总数
            'lxgc_hwk_sumcnt',  // 做作业总数
            'lxgc_pall',  // 查看疗效评估, 人数
            'lxgc_test_pcnt',  // 做巩固, 人数
            'lxgc_hwk_pcnt'); // 做作业, 人数
    }

    public static function getKeyDescArray () {
        return array(
            'allcnt' => '总人数',  // 总人数
            'sumcnt0' => '总报到人数',  // 总报到人数
            'yes_sumcnt0' => '服药人数,总',  // 服药人数,总
            'yes_zsd_cnt0' => '服择思达的人数,总',  // 服择思达的人数,总
            'yes_zzd_cnt0' => '服专注达的人数,总',  // 服专注达的人数,总
            'sumcnt1' => '人数,活跃',  // 人数,活跃
            'yes_sumcnt1' => '服药人数,活跃',  // 服药人数,活跃
            'yes_zsd_cnt1' => '服择思达的人数,活跃',  // 服择思达的人数,活跃
            'yes_zzd_cnt1' => '服专注达的人数,活跃',  // 服专注达的人数,活跃
            'pipe_sumcnt' => '当日流,总数',  // 当日流,总数
            'wxpicmsg_sumcnt' => '图片消息,总数',  // 图片消息,总数
            'wxtxtmsg_sumcnt' => '文本消息,总数',  // 文本消息,总数
            'answersheet_sumcnt' => '答卷,总数',  // 答卷,总数
            'patientnote_sumcnt' => '日记,总数',  // 日记,总数
            'fbt_sumcnt' => '课程作业,总数',  // 课程作业,总数
            'pipe_pcnt' => '有流,人数',  // 有流,人数
            'wxpicmsg_pcnt' => '有图片消息,人数',  // 有图片消息,人数
            'wxtxtmsg_pcnt' => '有文本消息,人数',  // 有文本消息,人数
            'answersheet_pcnt' => '有答卷,人数',  // 有答卷,人数
            'patientnote_pcnt' => '有日记,人数',  // 有日记,人数
            'fbt_pcnt' => '有课程作业,人数'); // 有课程作业,人数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_date_patient::createByBiz row cannot empty");

        $default = array();
        $default["thedate"] = '';
        $default["allcnt"] = 0;
        $default["sumcnt0"] = 0;
        $default["yes_sumcnt0"] = 0;
        $default["yes_zsd_cnt0"] = 0;
        $default["yes_zzd_cnt0"] = 0;
        $default["sumcnt1"] = 0;
        $default["yes_sumcnt1"] = 0;
        $default["yes_zsd_cnt1"] = 0;
        $default["yes_zzd_cnt1"] = 0;
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
    public function getMd () {
        return substr($this->thedate, 5);
    }

    // 报到率
    public function getBaodaoRate () {
        return sprintf("%.2f", $this->sumcnt0 * 100 / $this->allcnt);
    }

    // 服药率
    public function getMedicineRate () {
        return sprintf("%.2f", $this->yes_sumcnt0 * 100 / $this->allcnt);
    }

    // 活跃率
    public function getActivityRate () {
        if ($this->sumcnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->sumcnt1 * 100 / $this->sumcnt0);
    }

    // 报到用户服药率
    public function getMedicineRateOfBaodao () {
        if ($this->sumcnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_sumcnt0 * 100 / $this->sumcnt0);
    }

    // 服药用户活跃率
    public function getActivityRateOfMedicine () {
        if ($this->yes_sumcnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_sumcnt1 * 100 / $this->yes_sumcnt0);
    }

    // 择思达服药率
    public function getZsdRateOfMedicine () {
        if ($this->yes_sumcnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_zsd_cnt0 * 100 / $this->yes_sumcnt0);
    }

    // 专注达服药率
    public function getZzdRateOfMedicine () {
        if ($this->yes_sumcnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_zzd_cnt0 * 100 / $this->yes_sumcnt0);
    }

    // 择思达服药率
    public function getActivityZsdRateOfMedicine () {
        if ($this->yes_zsd_cnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_zsd_cnt1 * 100 / $this->yes_zsd_cnt0);
    }

    // 专注达活跃率
    public function getActivityZzdRateOfMedicine () {
        if ($this->yes_zzd_cnt0 < 1)
            return 0;
        return sprintf("%.2f", $this->yes_zzd_cnt1 * 100 / $this->yes_zzd_cnt0);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
