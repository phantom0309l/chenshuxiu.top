<?php
/*
 * GameCombatSoc
 */
class GameCombatSoc extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameid',  // gameid
            'gameplayid',  // gameplayid
            'startms',  // 毫秒
            'doms',  // 毫秒
            'num');        // 完成一次所用次数

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'gameplayid',
            'startms',
            'doms',
            'num');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["game"] = array(
            "type" => "Game",
            "key" => "gameid");
        $this->_belongtos["gameplay"] = array(
            "type" => "GamePlay",
            "key" => "gameplayid");
    }

    // $row = array();
    // $row["gameid"] = $gameid;
    // $row["gameplayid"] = $gameplayid;
    // $row["startms"] = $startms;
    // $row["doms"] = $doms;
    // $row["num"] = $num;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameCombatSoc::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["gameplayid"] = 0;
        $default["startms"] = 0;
        $default["doms"] = 0;
        $default["num"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
