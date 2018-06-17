<?php

/*
 * XHotelDao
 */
class XHotelDao extends Dao
{

    public static function getByName ($hotelname): XHotel {
        $cond = "and name=:hotelname ";
        $bind = [];
        $bind[':hotelname'] = $hotelname;
        return Dao::getEntityByCond('XHotel', $cond, $bind);
    }
}