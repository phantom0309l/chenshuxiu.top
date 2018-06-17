<?php
/*
 * Guest_schulterecord
 */
class Guest_schulterecord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'openid',  // 用户的标识，对当前公众号唯一
            'time',  // 用时，毫秒差值
            'role',  // 角色
            'toptime',  // 这个人的最好成绩
            'isshared',  // 是否分享
            'errornum',  // 错误数
            'remark'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["openid"] = $openid;
    // $row["time"] = $time;
    // $row["isshared"] = $isshared;
    // $row["errornum"] = $errornum;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Guest_schulterecord::createByBiz row cannot empty");

        $default = array();
        $default["openid"] = '';
        $default["time"] = 0;
        $default["role"] = 0;
        $default["toptime"] = 0;
        $default["isshared"] = 0;
        $default["errornum"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
