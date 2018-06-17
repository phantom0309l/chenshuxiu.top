<?php

/*
 * CronLog
 */
class CronLog extends Entity
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
            'crontabid',  // crontabid
            'thedate',  // 日期
            'begintime',  // begintime
            'endtime',  // endtime
            'brief',  // 日志摘要,特别关心的数据（推送总数之类的)
            'content'); // 日志明细,如果需要的话
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'crontabid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["crontab"] = array(
            "type" => "CronTab",
            "key" => "crontabid");
    }

    // $row = array();
    // $row["crontabid"] = $crontabid;
    // $row["thedate"] = $thedate;
    // $row["begintime"] = $begintime;
    // $row["endtime"] = $endtime;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CronLog::createByBiz row cannot empty");

        $default = array();
        $default["crontabid"] = 0;
        $default["thedate"] = '';
        $default["begintime"] = '';
        $default["endtime"] = '';
        $default["brief"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    public static function createByCron (CronTab $crontab) {
        $row = array();
        $row["crontabid"] = $crontab->id;
        $row["thedate"] = date('Y-m-d');
        $row["begintime"] = date('Y-m-d H:i:s');
        $row["endtime"] = '0000-00-00 00:00:00';
        $cronlog = CronLog::createByBiz($row);

        // 修正 commit_fix_cnt
        $unitOfWork = BeanFinder::get('UnitOfWork');
        $commit_fix_cnt = $unitOfWork->getInfoForXunitofwork('commit_fix_cnt');
        $unitOfWork->setInfoForXunitofwork('commit_fix_cnt', $commit_fix_cnt - 1);

        return $cronlog;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
}
