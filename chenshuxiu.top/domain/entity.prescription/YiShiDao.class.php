<?php

/*
 * YiShiDao
 */
class YiShiDao extends Dao
{

    public static function getOneYishi () {
        $yishis = Dao::getEntityListByCond('YiShi', ' and type=1 ');

        shuffle($yishis);

        return array_shift($yishis);
    }
}