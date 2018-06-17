<?php
/*
 * PvLog_hezuo
 */
class PvLog_hezuo extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'collectionid'    //一次提交的集合id
        ,'doctorid'    //doctorid
        ,'menu_code'    //入口标识
        ,'url'    //url
        ,'pos'    //访问顺序
        ,'in_time'    //进入页面时间
        ,'out_time'    //离开页面时间
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('collectionid' ,'doctorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    }

    // $row = array();
    // $row["collectionid"] = $collectionid;
    // $row["doctorid"] = $doctorid;
    // $row["menu_code"] = $menu_code;
    // $row["url"] = $url;
    // $row["pos"] = $pos;
    // $row["in_time"] = $in_time;
    // $row["out_time"] = $out_time;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"PvLog_hezuo::createByBiz row cannot empty");

        $default = array();
        $default["collectionid"] =  0;
        $default["doctorid"] =  0;
        $default["menu_code"] = '';
        $default["url"] = '';
        $default["pos"] =  0;
        $default["in_time"] = '';
        $default["out_time"] = '';

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
