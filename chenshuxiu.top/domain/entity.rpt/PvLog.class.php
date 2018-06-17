<?php

/*
 * PvLog
 */
class PvLog extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    public static function getKeysDefine () {
        return array(
            'collectionid',  // 一次提交的集合id
            'unitofworkid',  // 工作单元id
            'openid',  // 用户的标识，对当前公众号唯一
            'wxuserid',  // wxuserid
            'menu_code',  // 入口标识
            'url',  // url
            'action_method',  // action_method
            'pos',  // 访问顺序
            'in_time',  // 进入页面时间
            'out_time'); // 离开页面时间
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
    }

    // $row = array();
    // $row["collectionid"] = $collectionid;
    // $row["openid"] = $openid;
    // $row["wxuserid"] = $wxuserid;
    // $row["menu_code"] = $menu_code;
    // $row["url"] = $url;
    // $row["action_method"] = $action_method;
    // $row["pos"] = $pos;
    // $row["in_time"] = $in_time;
    // $row["out_time"] = $out_time;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PvLog::createByBiz row cannot empty");

        $default = array();
        $default["collectionid"] = 0;
        $default["unitofworkid"] = 0;
        $default["openid"] = '';
        $default["wxuserid"] = 0;
        $default["menu_code"] = '';
        $default["url"] = '';
        $default["action_method"] = '';
        $default["pos"] = 0;
        $default["in_time"] = '0000-00-00 00:00:00';
        $default["out_time"] = '0000-00-00 00:00:00';

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
