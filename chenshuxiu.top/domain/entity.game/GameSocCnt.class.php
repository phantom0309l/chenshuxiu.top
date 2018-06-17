<?php
/*
 * GameSocCnt
 */
class GameSocCnt extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'gameplayid',  //
            'wxuserid',  // wxuserid
            'allcnt',  // 总步数
            'numavg',  // 步数平均值
            'avg'); // 用时平均值

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
    // $row["allcnt"] = $allcnt;
    // $row["numavg"] = $numavg;
    // $row["avg"] = $avg;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GameSocCnt::createByBiz row cannot empty");

        $default = array();
        $default["gameplayid"] = 0;
        $default["wxuserid"] = 0;
        $default["allcnt"] = 0;
        $default["numavg"] = '';
        $default["avg"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getOrCreateByGameplayid ($gameplayid) {
        $gameplay = GamePlay::getById($gameplayid);

        $entity = GameSocCntDao::getByGameplayid($gameplayid);
        if (false == $entity instanceof GameSocCnt) {
            $avg = GameCombatSocDao::getAvgTime($gameplayid);
            $numavg = GameCombatSocDao::getAvgNum($gameplayid);
            $allcnt = 0 + GameCombatSocDao::getSumCnt($gameplayid);

            $row = array();
            $row["wxuserid"] = $gameplay->wxuserid;
            $row["gameplayid"] = $gameplayid;
            $row["avg"] = $avg;
            $row["numavg"] = $numavg;
            $row["allcnt"] = $allcnt;
            $entity = GameSocCnt::createByBiz($row);

            $gameplay->objtype = "GameSocCnt";
            $gameplay->objid = $entity->id;
        }
        return $entity;
    }

}
