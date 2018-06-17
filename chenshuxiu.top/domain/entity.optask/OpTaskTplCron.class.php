<?php

/*
 * OpTaskTplCron
 */

class OpTaskTplCron extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
              'optasktplid'    //
            , 'step'    //步骤
            , 'send_content'    //发送话术
            , 'dealwith_type'    //后续处理方式 unfinish：未完成 hang_up：挂起 appoint_follow：约定跟进
            , 'follow_daycnt'    //约定跟进的天数
            , 'remark'
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('optasktplid',);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["optasktpl"] = array("type" => "OpTaskTpl", "key" => "optasktplid");
    }

    // $row = array();
    // $row["optasktplid"] = $optasktplid;
    // $row["step"] = $step;
    // $row["send_content"] = $send_content;
    // $row["dealwith_type"] = $dealwith_type;
    // $row["follow_daycnt"] = $follow_daycnt;
    // $row["remark"] = '';
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "OpTaskTplCron::createByBiz row cannot empty");

        $default = array();
        $default["optasktplid"] = 0;
        $default["step"] = 0;
        $default["send_content"] = '';
        $default["dealwith_type"] = '';
        $default["follow_daycnt"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDealwith_typeStr () {
        $arr = self::getDealwith_types();

        return $arr["{$this->dealwith_type}"];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getDealwith_types () {
        $arr = [
            'unfinish' => '未完成',
            'hang_up' => '挂起',
            'appoint_follow' => '约定跟进'
        ];

        return $arr;
    }

    public static function getFollow_daycnts () {
        $list = [];
        for ($i = 1; $i <= 30; $i++) {
            $list["{$i}"] = $i;
        }

        return $list;
    }

    public static function getSteps () {
        $list = [];
        for ($i = 1; $i <= 10; $i++) {
            $list["{$i}"] = $i;
        }

        return $list;
    }

}
    