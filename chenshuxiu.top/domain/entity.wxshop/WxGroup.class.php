<?php
/*
 * WxGroup
 */
class WxGroup extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'wxshopid'    //wxshopid
        ,'groupid'    //来自微信
        ,'ename'    //ename
        ,'name'    //name
        ,'content'    //介绍
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    $this->_belongtos["wxshop"] = array ("type" => "Wxshop", "key" => "wxshopid" );
    $this->_belongtos["group"] = array ("type" => "Group", "key" => "groupid" );
    }

    // $row = array();
    // $row["wxshopid"] = $wxshopid;
    // $row["groupid"] = $groupid;
    // $row["ename"] = $ename;
    // $row["name"] = $name;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"WxGroup::createByBiz row cannot empty");

        $default = array();
        $default["wxshopid"] =  0;
        $default["groupid"] =  0;
        $default["ename"] = '';
        $default["name"] = '';
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
