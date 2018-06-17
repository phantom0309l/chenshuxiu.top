<?php
/*
 * GuestRecord
 */
class GuestRecord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'type',  // type
            'wxuserid',  // wxuserid
            'name',  // name
            'rightnum',  // rightnum
            'score',  // score
            'content',  //
            'forwarded'); //

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["type"] = $type;
    // $row["name"] = $name;
    // $row["rightnum"] = $rightnum;
    // $row["score"] = $score;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "GuestRecord::createByBiz row cannot empty");

        $default = array();
        $default["type"] = '';
        $default["wxuserid"] = 0;
        $default["name"] = '';
        $default["rightnum"] = 0;
        $default["score"] = 0;
        $default["content"] = '';
        $default["forwarded"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
