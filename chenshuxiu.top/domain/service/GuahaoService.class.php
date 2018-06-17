<?php

// 创建: 20170801 by txj
class GuahaoService
{
    // 获取默认挂号费
    public static function getDefaultPrice () {
        return 8000;
    }

    public static function getPrice_yuan () {
        $n = self::getDefaultPrice();
        return $n/100;
    }
}
