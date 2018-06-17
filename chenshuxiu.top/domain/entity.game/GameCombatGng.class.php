<?php
/*
 * GameCombatGng
 */
class GameCombatGng extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameid',  // gameid
            'gameplayid',  // gameplayid
            'dif',  // 相差毫秒值
            'clicktype');        // 0表示没点，1表示点了绿块，2表示点击了红块

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'gameplayid',
            'dif');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["game"] = array(
            "type" => "Game",
            "key" => "gameid");
        $this->_belongtos["gameplay"] = array(
            "type" => "Gameplay",
            "key" => "gameplayid");
        $this->_belongtos["d"] = array(
            "type" => "D",
            "key" => "dif");
    }

    // $row = array();
    // $row["gameid"] = $gameid;
    // $row["gameplayid"] = $gameplayid;
    // $row["dif"] = $dif;
    // $row["clicktype"] = $clicktype;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameCombatGng::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["gameplayid"] = 0;
        $default["dif"] = 0;
        $default["clicktype"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
