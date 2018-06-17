<?php
/*
 * Fbt_gamerate
 */

class Fbt_gamerate extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',  // userid, users表的id，不是fbt_users表的id
            'gamename',  // 游戏名称
            'timeconsuming',  // 游戏中所花费的时间
            'content'); // 游戏结果 json
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid',
            'gamename',
            'timeconsuming',
            'content');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["gamename"] = $gamename;
    // $row["timeconsuming"] = $timeconsuming;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Fbt_gamerate::createByBiz row cannot empty");

        $default = array();
        $default["userid"] = 0;
        $default["gamename"] = '';
        $default["timeconsuming"] = 0;
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getLastByUserid ($userid) {
        $sql = "SELECT * FROM fbt_gamerates WHERE userid = :userid ORDER BY id DESC LIMIT 1";

        $bind = [];
        $bind[':userid'] = $userid;
        return Dao::loadEntity('Fbt_gamerate', $sql, $bind);
    }
}
