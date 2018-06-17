<?php
/*
 * GameCombatMb
 */
class GameCombatMb extends Entity
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
            'score',  // 本轮得分
            'blocknum',  // 本轮块数
            'isright');        // 是否正确

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'gameplayid',
            'startms',
            'doms',
            'score');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["game"] = array(
            "type" => "Game",
            "key" => "gameid");
        $this->_belongtos["gameplay"] = array(
            "type" => "Gameplay",
            "key" => "gameplayid");
        $this->_belongtos["start"] = array(
            "type" => "Start",
            "key" => "startms");
        $this->_belongtos["do"] = array(
            "type" => "Do",
            "key" => "doms");
        $this->_belongtos["sco"] = array(
            "type" => "Sco",
            "key" => "score");
    }

    // $row = array();
    // $row["gameid"] = $gameid;
    // $row["gameplayid"] = $gameplayid;
    // $row["startms"] = $startms;
    // $row["doms"] = $doms;
    // $row["score"] = $score;
    // $row["blocknum"] = $blocknum;
    // $row["isright"] = $isright;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameCombatMb::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["gameplayid"] = 0;
        $default["startms"] = 0;
        $default["doms"] = 0;
        $default["score"] = 0;
        $default["blocknum"] = 0;
        $default["isright"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
