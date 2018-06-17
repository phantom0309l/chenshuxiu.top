<?php

/**
 * LatLngEntity
 * @desc 有经纬度的实体
 */

class LatLngEntity extends Entity
{

    public static function rad ($d) {
        return (double) ($d * 3.1415926535898 / 180.0);
    }

    public static function getDistance ($lat1, $lng1, $lat2, $lng2) {
        $EARTH_RADIUS = 6378.137;
        $radLat1 = self::rad($lat1);
        $radLat2 = self::rad($lat2);

        $a = $radLat1 - $radLat2;
        $b = self::rad($lng1) - self::rad($lng2);
        $s = $EARTH_RADIUS * 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = round($s * 10000) / 10000;
        return $s;
    }

    public static function getDistanceSqlCond ($lat, $lng, $as = '') {
        return " (2*6378.137*ASIN(SQRT(POW(SIN(PI()*({$as}lat-{$lat})/360),2)+COS(PI()*{$as}lat/180)*COS({$lat}*PI()/180)*POW(SIN(PI()*({$as}lng-{$lng})/360),2)))) ";
    }
}