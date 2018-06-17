<?php

/*
 * BedTktConfig
 */

class BedTktConfig extends Entity
{

    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
            'doctorid'    //doctorid
        , 'typestr'    //类型 1-治疗  2-检查
        , 'is_allow_bedtkt'    //总开关 1开启   0关闭
        , 'content'    //压缩内容，json格式
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array(
            'doctorid',);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array("type" => "Doctor", "key" => "doctorid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["typestr"] = $typestr;
    // $row["is_allow_bedtkt"] = $is_allow_bedtkt;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "BedTktConfig::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["typestr"] = '';
        $default["is_allow_bedtkt"] = 0;
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
