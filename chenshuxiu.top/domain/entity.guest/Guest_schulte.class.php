<?php
/*
 * Guest_schulte
 */
class Guest_schulte extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'guestid',  // guestid
            'fromguestid',  // fromguestid
            'toptime',  // 家长最好成绩，毫秒差值
            'toptime1',  // 孩子最好成绩，毫秒差值
            'sharenum',
            'remark');        // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["guest"] = array(
            "type" => "Guest",
            "key" => "guestid");
    }

    // $row = array();
    // $row["guestid"] = $guestid;
    // $row["fromguestid"] = $fromguestid;
    // $row["toptime"] = $toptime;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Guest_schulte::createByBiz row cannot empty");

        $default = array();
        $default["guestid"] = 0;
        $default["fromguestid"] = 0;
        $default["sharenum"] = 0;
        $default["toptime"] = 0;
        $default["toptime1"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
