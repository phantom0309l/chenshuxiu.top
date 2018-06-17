<?php
// CronTask
// 定时任务

// owner by xuzhe
// create by xuzhe
// review by sjp 20160628

class CronTask extends Entity
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
            'cronprocessid',  // cronprocessid
            'tasktype',  // 定时任务类名
            'plantime',  // 计划时间
            'iswait',  // 状态, 1 待执行, 0 不需要执行
            'isdone',  // 状态
            'donetime',  // donetime
            'content'); // 执行结果

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'cronprocessid',
            'tasktype');
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

        $this->_belongtos["cronprocess"] = array(
            "type" => "CronProcess",
            "key" => "cronprocessid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["cronprocessid"] = $cronprocessid;
    // $row["tasktype"] = $tasktype;
    // $row["iswait"] = $iswait;
    // $row["isdone"] = $isdone;
    // $row["donetime"] = $donetime;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CronTask::createByBiz row cannot empty");

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
        $default["cronprocessid"] = 0;
        $default["tasktype"] = '';
        $default["plantime"] = XDateTime::now();
        $default["iswait"] = 1;
        $default["isdone"] = 0;
        $default["donetime"] = '0000-00-00 00:00:00';
        $default["content"] = '';

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
