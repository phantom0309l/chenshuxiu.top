<?php
/*
 * GameGngCnt
 */
class GameGngCnt extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameplayid',  //
            'wxuserid',  // wxuserid
            'rightcnt',  // 正确数
            'misscnt',  // miss数
            'errorcnt',  // 错误数
            'allcnt',  // 总数
            'rightrate',  // 正确率
            'rightavg'); // 正确时的平均响应时间

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'gameplayid',
            'wxuserid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
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
    // $row["misscnt"] = $misscnt;
    // $row["errorcnt"] = $errorcnt;
    // $row["allcnt"] = $allcnt;
    // $row["rightrate"] = $rightrate;
    // $row["rightavg"] = $rightavg;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameGngCnt::createByBiz row cannot empty");

        $default = array();
        $default["gameplayid"] = 0;
        $default["wxuserid"] = 0;
        $default["rightcnt"] = 0;
        $default["misscnt"] = 0;
        $default["errorcnt"] = 0;
        $default["allcnt"] = 0;
        $default["rightrate"] = '';
        $default["rightavg"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getOrCreateByGameplayid ($gameplayid) {
        $gameplay = GamePlay::getById($gameplayid);

        $entity = GameGngCntDao::getByGameplayid($gameplayid);
        if (false == $entity instanceof GameGngCnt) {
            $blcs = GameCombatGngDao::getByGameplayid($gameplayid);
            $rblcs = GameCombatGngDao::getIsRight($gameplayid);
            $mblcs = GameCombatGngDao::getIsMiss($gameplayid);
            $eblcs = GameCombatGngDao::getIsError($gameplayid);
            $rightavg = GameCombatGngDao::getRightAvgTime($gameplayid);
            $all = count($blcs);
            $rightnum = count($rblcs);
            $missnum = count($mblcs);
            $errornum = count($eblcs);
            $rate = $all === 0 ? 0 : round($rightnum / $all, 2) * 100;

            $row = array();
            $row["wxuserid"] = $gameplay->wxuserid;
            $row["gameplayid"] = $gameplayid;
            $row["rightcnt"] = $rightnum;
            $row["misscnt"] = $missnum;
            $row["errorcnt"] = $errornum;
            $row["allcnt"] = $all;
            $row["rightrate"] = $rate;
            $row["rightavg"] = $rightavg;
            $entity = GameGngCnt::createByBiz($row);

            $gameplay->objtype = "GameGngCnt";
            $gameplay->objid = $entity->id;
        }
        return $entity;
    }
}
