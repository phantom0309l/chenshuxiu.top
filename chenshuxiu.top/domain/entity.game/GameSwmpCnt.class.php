<?php
/*
 * GameSwmpCnt
 */
class GameSwmpCnt extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameplayid',  //
            'wxuserid',  // wxuserid
            'rightcnt',  // 正确数
            'allcnt',  // 总数
            'rightrate',  // 正确率
            'avg'); // 平均值

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
    // $row["allcnt"] = $allcnt;
    // $row["rightrate"] = $rightrate;
    // $row["avg"] = $avg;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameSwmpCnt::createByBiz row cannot empty");

        $default = array();
        $default["gameplayid"] = 0;
        $default["wxuserid"] = 0;
        $default["rightcnt"] = 0;
        $default["allcnt"] = 0;
        $default["rightrate"] = '';
        $default["avg"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getOrCreateByGameplayid ($gameplayid) {
        $gameplay = GamePlay::getById($gameplayid);

        $entity = GameSwmpCntDao::getByGameplayid($gameplayid);
        if (false == $entity instanceof GameSwmpCnt) {
            $blcs = GameCombatSwmpDao::getByGameplayid($gameplayid);
            $rblcs = GameCombatSwmpDao::getIsRight($gameplayid);
            $avg = GameCombatSwmpDao::getAvgTime($gameplayid);
            $all = count($blcs);
            $rightnum = count($rblcs);
            $rate = $all === 0 ? 0 : round($rightnum / $all, 2) * 100;

            $row = array();
            $row["wxuserid"] = $gameplay->wxuserid;
            $row["gameplayid"] = $gameplayid;
            $row["rightcnt"] = $rightnum;
            $row["allcnt"] = $all;
            $row["rightrate"] = $rate;
            $row["avg"] = $avg;
            $entity = GameSwmpCnt::createByBiz($row);

            $gameplay->objtype = "GameSwmpCnt";
            $gameplay->objid = $entity->id;
        }
        return $entity;
    }

}
