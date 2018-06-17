<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-6-24
 * Time: 上午11:29
 */
class PhoneUtil
{
    // 获取app版本
    public static function getVersion () {
        $version = $_SERVER['HTTP_VERSION'];

        return $version;
    }

}