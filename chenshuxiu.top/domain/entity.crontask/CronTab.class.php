<?php

/*
 * CronTab
 */
class CronTab extends Entity
{

    const dbfix = 'dbfix';

    const opjob = 'opjob';

    const optask = 'optask';

    const pushmsg = 'pushmsg';

    const rpt = 'rpt';

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'lastcrontime',  // 末次执行时间
            'process_name',  // 脚本类名
            'type',  // 类型: 数据修复, 推送消息, 任务生成, 报表 等
            'when',  // 执行时机,频率
            'title',  // 脚本中文名
            'content',  // 脚本说明
            'filepath',  // 脚本文件路径
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'process_name');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["lastcrontime"] = $lastcrontime;
    // $row["process_name"] = $process_name;
    // $row["type"] = $type;
    // $row["when"] = $when;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["filepath"] = $filepath;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CronTab::createByBiz row cannot empty");

        $default = array();
        $default["lastcrontime"] = '0000-00-00 00:00:00';
        $default["process_name"] = '';
        $default["type"] = '';
        $default["when"] = '';
        $default["title"] = '';
        $default["content"] = '';
        $default["filepath"] = '';
        $default["status"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getLastCronLogTimeSpan () {
        $cronlog = CronLogDao::getLastOneByCronTab($this);

        $str = "--";
        if ($cronlog instanceof CronLog) {
            $str = $cronlog->begintime;
            if ($cronlog->endtime == '0000-00-00 00:00:00') {
                $str .= " 至 <span class='red'>" . substr($cronlog->endtime, - 8) . "</span>";
            } else {
                $str .= " 至 " . substr($cronlog->endtime, - 8);
            }
        }

        return $str;
    }

    // 获取最新的执行日志
    public function getLastCronLog () {
        return CronLogDao::getLastOneByCronTab($this);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getTypeDescArr () {
        $arr = array(
            self::dbfix => '修数据库数据',
            self::opjob => 'opjob类型任务',
            self::optask => 'optask类型任务',
            self::pushmsg => '推送消息',
            self::rpt => '报表');

        return $arr;
    }
}
