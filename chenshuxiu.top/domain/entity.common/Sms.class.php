<?php
// Sms
// 短信发送日志

// owner by sjp
// create by sjp
// review by sjp 20160628
// TODO rework 还没有建表

class Sms extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array( //
            "mobile",  // 手机号
            "type",  // 短信类型
            "content",  // 短信内容
            "errcode",  // 返回结果
            "errmsg",  // 返回结果
            "status"); // 状态

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            "mobile");
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array ();
    // $row ['mobile'] = $mobile;
    // $row ['type'] = $type;
    // $row ['content'] = $content;
    // $row ['errcode'] = $errcode;
    // $row ['errmsg'] = $errmsg;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Sms::createByBiz row cannot empty");

        $default = array();
        $default['type'] = 0;
        $default['errcode'] = "";
        $default['errmsg'] = "";
        $default['status'] = 1;

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
