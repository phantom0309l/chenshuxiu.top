<?php

/*
 * XHotel
 */
class XHotel extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 名称
            'address'); // 地址
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["address"] = $address;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XHotel::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["address"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getNameWithRedMark () {
        $xx = $this->id % 100;
        if ($xx > 50) {
            $str = "<span style='color:red'>{$this->name}</span>";
        } else {
            $str = $this->name;
        }

        return $str;
    }

    public function getXRoomNum () {
        $list = $this->getXRooms();
        return count($list);
        return XRoomDao::getXRoomNumByXHotel($this);
    }

    public function getXRooms () {
        return XRoomDao::getXRoomsByXHotel($this);
    }

    // 获取 本酒店 指定房间
    public function getXRoomByNo ($roomno) {
        return XRoomDao::getByXHotelRoomno($this, $roomno);
    }
}
