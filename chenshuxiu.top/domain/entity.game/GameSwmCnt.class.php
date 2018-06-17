<?php
/*
 * GameSwmCnt
 */
class GameSwmCnt extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameplayid',
            'wxuserid',  // wxuserid
            'firstresponsetime',  // 首次平均反应时间
            'tokensearchtime',  // 平均启动搜素时间
            'lastresponsetime',  // 找最后一个宝物所用时间
            'outerror',  // 外部错误数
            'innererror',  // 内部错误数
            'botherror',  // 双错误数
            'allerror',  // 总错误数
            'clicknum'); // 总点击数

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["firstresponsetime"] = $firstresponsetime;
    // $row["tokensearchtime"] = $tokensearchtime;
    // $row["lastresponsetime"] = $lastresponsetime;
    // $row["outerror"] = $outerror;
    // $row["innererror"] = $innererror;
    // $row["botherror"] = $botherror;
    // $row["allerror"] = $allerror;
    // $row["clicknum"] = $clicknum;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameSwmCnt::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["gameplayid"] = 0;
        $default["firstresponsetime"] = 0;
        $default["tokensearchtime"] = 0;
        $default["lastresponsetime"] = 0;
        $default["outerror"] = 0;
        $default["innererror"] = 0;
        $default["botherror"] = 0;
        $default["allerror"] = 0;
        $default["clicknum"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getOrCreateByGameplayid ($gameplayid) {
        $gameplay = GamePlay::getById($gameplayid);

        $entity = GameSwmCntDao::getByGameplayid($gameplayid);
        if (false == $entity instanceof GameSwmCnt) {
            $swms = GameCombatSwmDao::getByGameplayid($gameplayid);
            $firstresponsetimesum = GameCombatSwmDao::getSum($gameplayid, "firstresponsetime");
            $tokensearchtimesum = GameCombatSwmDao::getSum($gameplayid, "tokensearchtime");
            $lastresponsetimesum = GameCombatSwmDao::getSum($gameplayid, "lastresponsetime");
            $all = count($swms);

            $firstresponsetimeavg = round($firstresponsetimesum / ($all * 1000), 2);
            $tokensearchtimeavg = round($tokensearchtimesum / ($all * 1000), 2);
            $lastresponsetimeavg = round($lastresponsetimesum / ($all * 1000), 2);

            $outerrorsum = GameCombatSwmDao::getSum($gameplayid, "outerror");
            $innererrorsum = GameCombatSwmDao::getSum($gameplayid, "innererror");
            $botherrorsum = GameCombatSwmDao::getSum($gameplayid, "botherror");
            $allerrorsum = $outerrorsum + $innererrorsum;
            $clicknumsum = GameCombatSwmDao::getSum($gameplayid, "clicknum");

            $row = array();
            $row["wxuserid"] = $gameplay->wxuserid;
            $row["gameplayid"] = $gameplayid;
            $row["firstresponsetime"] = $firstresponsetimeavg;
            $row["tokensearchtime"] = $tokensearchtimeavg;
            $row["lastresponsetime"] = $lastresponsetimeavg;
            $row["outerror"] = $outerrorsum;
            $row["innererror"] = $innererrorsum;
            $row["botherror"] = $botherrorsum;
            $row["allerror"] = $allerrorsum;
            $row["clicknum"] = $clicknumsum;
            $entity = GameSwmCnt::createByBiz($row);

            $gameplay->objtype = "GameSwmCnt";
            $gameplay->objid = $entity->id;
        }
        return $entity;
    }

}
