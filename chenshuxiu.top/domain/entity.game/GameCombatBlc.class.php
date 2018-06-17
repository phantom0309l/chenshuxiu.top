<?php
/*
 * GameCombatBlc
 */
class GameCombatBlc extends Entity
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
            'showisbl',  // 左大右小
            'showcolor',  // 颜色
            'rightoption',  // 正确选项
            'isright'); // 是否正确

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'gameplayid');
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
    // $row["showisbl"] = $showisbl;
    // $row["showcolor"] = $showcolor;
    // $row["rightoption"] = $rightoption;
    // $row["isright"] = $isright;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameCombatBlc::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["gameplayid"] = 0;
        $default["startms"] = 0;
        $default["doms"] = 0;
        $default["showisbl"] = 0;
        $default["showcolor"] = 0;
        $default["rightoption"] = 0;
        $default["isright"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
