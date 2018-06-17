<?php
/*
 * GameCombatSwm
 */
class GameCombatSwm extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameid',  // gameid
            'gameplayid',  // gameplayid
            'firstresponsetime',  // 首次平均反应时间
            'tokensearchtime',  // 平均启动搜素时间
            'lastresponsetime',  // 找最后一个宝物所用时间
            'outerror',  // 外部错误
            'innererror',  // 内部错误
            'botherror',  // 双错误数
            'clicknum',  // 本轮寻找宝物所点击数
            'boxnum'); // 盒子数

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameid',
            'gameplayid',
            'firstresponsetime',
            'tokensearchtime',
            'lastresponsetime');
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
    // $row["firstresponsetime"] = $firstresponsetime;
    // $row["tokensearchtime"] = $tokensearchtime;
    // $row["lastresponsetime"] = $lastresponsetime;
    // $row["outerror"] = $outerror;
    // $row["innererror"] = $innererror;
    // $row["botherror"] = $botherror;
    // $row["clicknum"] = $clicknum;
    // $row["boxnum"] = $boxnum;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameCombatSwm::createByBiz row cannot empty");

        $default = array();
        $default["gameid"] = 0;
        $default["gameplayid"] = 0;
        $default["firstresponsetime"] = 0;
        $default["tokensearchtime"] = 0;
        $default["lastresponsetime"] = 0;
        $default["outerror"] = 0;
        $default["innererror"] = 0;
        $default["botherror"] = 0;
        $default["clicknum"] = 0;
        $default["boxnum"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
