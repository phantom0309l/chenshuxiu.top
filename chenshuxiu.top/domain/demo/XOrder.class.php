<?php

/*
 * XOrder
 */
class XOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xhotelid',  // xcustomerid
            'xroomid',  // xroomid
            'xcustomerid',  // xcustomerid
            'thedate',  //
            'daycnt'); // 天数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xhotelid',
            'xroomid',
            'xcustomerid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["xhotel"] = array(
            "type" => "XHotel",
            "key" => "xhotelid");

        $this->_belongtos["xroom"] = array(
            "type" => "XRoom",
            "key" => "xroomid");

        $this->_belongtos["xcustomer"] = array(
            "type" => "XCustomer",
            "key" => "xcustomerid");
    }

    // $row = array();
    // $row["xhotelid"] = $xhotelid;
    // $row["xroomid"] = $xroomid;
    // $row["xcustomerid"] = $xcustomerid;
    // $row["thedate"] = $thedate;
    // $row["daycnt"] = $daycnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XOrder::createByBiz row cannot empty");

        $default = array();
        $default["xhotelid"] = 0;
        $default["xroomid"] = 0;
        $default["xcustomerid"] = 0;
        $default["thedate"] = '';
        $default["daycnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
