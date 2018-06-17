<?php

/*
 * 执业医师
 */

class YiShi extends Entity
{

    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
            'name',  // 姓名
            'mobile',  // 手机号,用于登录
            'password',  // 密码
            'hospital_name',  // 医院名
            'department_name',  // 科室名
            'type',  // 权限类型
            'last_login_time'); // 最后登录时间
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["mobile"] = $mobile;
    // $row["password"] = $password;
    // $row["hospital_name"] = $hospital_name;
    // $row["department_name"] = $department_name;
    // $row["type"] = $type;
    // $row["last_login_time"] = $last_login_time;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "YiShi::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["mobile"] = '';
        $default["password"] = '';
        $default["hospital_name"] = '';
        $default["department_name"] = '';
        $default["type"] = 0;
        $default["last_login_time"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
