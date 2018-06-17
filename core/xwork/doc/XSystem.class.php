<?php

class XSystem
{
    // 系统初始化函数
    public static function init () {
        Config::setConfigFile(dirname(__FILE__) . "/../sys/config.properties.php");
        XSessionManager::init(Config::getConfig('website_domain'));
    }
}