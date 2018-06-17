<?php

/*
 * AuditorOpLog
 */

class AuditorOpLog extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
             'patientid'    //patientid
            ,'auditorid'    //auditorid
            , 'code'    //类型 例如optask,patientrecord...
            , 'content'    //操作具体内容
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('auditorid',);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["auditor"] = array("type" => "Auditor", "key" => "auditorid");
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["auditorid"] = $auditorid;
    // $row["code"] = $code;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "AuditorOpLog::createByBiz row cannot empty");

        $default = [];
        $default["patientid"] = 0;
        $default["auditorid"] = 0;
        $default["code"] = '';
        $default["content"] = '';

        $row += $default;

        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getCodes () {
        $list = [
            '' => '全部',
            'optask' => '任务',
            'patientrecord' => '运营备注',
            'patientstage' => '患者阶段',
            'patientgroup' => '患者分组',
            'patientremark' => '患者文本备注',
        ];

        return $list;
    }

    public function getCodeStr () {
        $codes = self::getCodes();

        return $codes["{$this->code}"];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 入nsq队列
    public static function nsqPush ($row) {
        $json = json_encode($row, JSON_UNESCAPED_UNICODE);

        // gearman 异步推送
        $job = Job::getInstance();
        $job->doBackground('auditoroplog', $json);
    }
}
    