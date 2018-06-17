<?php

/*
 * XRoom
 */
class XRoom extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xhotelid',  // xhotelid
            'no',  //
            'name',  //
            'price'); // 价格, 分
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xhotelid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["xhotel"] = array(
            "type" => "XHotel",
            "key" => "xhotelid");
    }

    // $row = array();
    // $row["xhotelid"] = $xhotelid;
    // $row["no"] = $no;
    // $row["name"] = $name;
    // $row["price"] = $price;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XRoom::createByBiz row cannot empty");

        $default = array();
        $default["xhotelid"] = 0;
        $default["no"] = '';
        $default["name"] = '';
        $default["price"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
