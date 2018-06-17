<?php
/*
 * Game
 */
class Game extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 游戏名称
            'ename',  // 游戏英文名称
            'pictureid',  // 游戏封面
            'brief',  // 简介
            'content'); // 文本内容

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["ename"] = $ename;
    // $row["pictureid"] = $pictureid;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Game::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["ename"] = '';
        $default["pictureid"] = 0;
        $default["brief"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }
}
