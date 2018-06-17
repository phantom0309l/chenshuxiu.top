<?php

/*
 * GamePlay
 */
class GamePlay extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameid',  // gameid
            'wxuserid',  // wxuserid
            'objtype',  // objtype
            'objid'); // objid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'wxuserid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["game"] = array(
            "type" => "Game",
            "key" => "gameid");
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["gameid"] = $gameid;
    // $row["wxuserid"] = $wxuserid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GamePlay::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["wxuserid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////
}
