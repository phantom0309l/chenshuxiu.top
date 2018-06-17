<?php

class MQ
{
    // 插入一条
    public static function push ($queueName = 'chip', $value) {
        Debug::trace("-- MQ:push $queueName => $value --");
        $redis = XRedis::getConnect();
        return $redis->lpush($queueName, $value);
    }

    // 取一条
    public static function pop ($queueName = 'chip') {
        $redis = XRedis::getConnect();
        return $redis->rpop($queueName);
    }

    // 获取队列长度
    public static function getLength ($queueName = 'chip') {
        $redis = XRedis::getConnect();
        return $redis->llen($queueName);
    }

    // 获取一批数据
    public static function getRange ($queueName = 'chip', $len = -1) {
        $redis = XRedis::getConnect();
        return $redis->lrange($queueName, 0, $len);
    }
}
