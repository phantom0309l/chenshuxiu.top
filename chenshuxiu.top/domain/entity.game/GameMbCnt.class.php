<?php
/*
 * GameMbCnt
 */
class GameMbCnt extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameplayid',  //
            'wxuserid',  // wxuserid
            'rightcnt',  // 正确数
            'errorcnt',  // 错误数
            'allcnt',  // 总数
            'rightrate',  // 正确率
            'scores'); // 总得分

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameplayid',
            'wxuserid');
    }

    protected function init_belongtos () {
        $this->_belongtos["gameplay"] = array(
            "type" => "GamePlay",
            "key" => "gameplayid");
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
    }

    // $row = array();
    // $row["gameplayid"] = $gameplayid;
    // $row["wxuserid"] = $wxuserid;
    // $row["rightcnt"] = $rightcnt;
    // $row["errorcnt"] = $errorcnt;
    // $row["allcnt"] = $allcnt;
    // $row["rightrate"] = $rightrate;
    // $row["scores"] = $scores;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameMbCnt::createByBiz row cannot empty");

        $default = array();
        $default["gameplayid"] = 0;
        $default["wxuserid"] = 0;
        $default["rightcnt"] = 0;
        $default["errorcnt"] = 0;
        $default["allcnt"] = 0;
        $default["rightrate"] = '';
        $default["scores"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getOrCreateByGameplayid ($gameplayid) {
        $gameplay = GamePlay::getById($gameplayid);

        $entity = GameMbCntDao::getByGameplayid($gameplayid);
        if (false == $entity instanceof GameMbCnt) {
            $blcs = GameCombatMbDao::getByGameplayid($gameplayid);
            $rblcs = GameCombatMbDao::getIsRight($gameplayid);
            $scores = GameCombatMbDao::getScores($gameplayid);
            $all = count($blcs);
            $rightnum = count($rblcs);
            $errornum = $all - $rightnum;
            $rate = $all === 0 ? 0 : round($rightnum / $all, 2) * 100;

            $row = array();
            $row["wxuserid"] = $gameplay->wxuserid;
            $row["gameplayid"] = $gameplayid;
            $row["rightcnt"] = $rightnum;
            $row["errorcnt"] = $errornum;
            $row["allcnt"] = $all;
            $row["rightrate"] = $rate;
            $row["scores"] = $scores;
            $entity = GameMbCnt::createByBiz($row);

            $gameplay->objtype = "GameMbCnt";
            $gameplay->objid = $entity->id;
        }
        return $entity;
    }

}
