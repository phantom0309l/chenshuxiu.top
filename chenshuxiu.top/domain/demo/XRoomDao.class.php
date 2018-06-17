<?php

/*
 * XRoomDao
 */
class XRoomDao extends Dao
{

    public static function getXRoomNumByXHotel (XHotel $xhotel) {
        $sql = "select count(*) from xrooms where xhotelid=:xhotelid ";
        $bind = [];
        $bind[':xhotelid'] = $xhotel->id;
        return Dao::queryValue($sql, $bind);
    }

    public static function getXRoomsByXHotel (XHotel $xhotel) {
        $cond = "and xhotelid=:xhotelid ";
        $bind = [];
        $bind[':xhotelid'] = $xhotel->id;
        return Dao::getEntityListByCond('XRoom', $cond, $bind);
    }

    public static function getByXHotelRoomno (XHotel $xhotel, $roomno) {
        $cond = "and xhotelid=:xhotelid and no=:roomno";
        $bind = [];
        $bind[':xhotelid'] = $xhotel->id;
        $bind[':roomno'] = $roomno;
        return Dao::getEntityByCond('XRoom', $cond, $bind);
    }
}