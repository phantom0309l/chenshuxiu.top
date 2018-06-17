<?php
class XCache {
    //@param encode would be ''|json|php
    public static function getValue($key, $expire, $f, $encode='') {
        $redis = XRedis::getConnect();
        Debug::trace(__METHOD__ . " get value with key($key) from cache");
        $val = $redis->get($key);
        if ($val === false) {
            $val = $f();
            $val_encode = self::encode($val, $encode);
            $redis->set($key, $val_encode, $expire);
            Debug::trace(__METHOD__ . " set value with key($key) to cache");
        } else {
            $val = self::decode($val, $encode);
        }
        return $val;
    }

    public static function removeCacheWithKey($key) {
        $redis = XRedis::getConnect();
        Debug::trace(__METHOD__ . " del cache with key($key)");
        return $redis->del($key);
    }

    private static function encode($val, $type='') {
        $ret = '';
        if ($type == 'json') {
            $ret = json_encode($val);
        } else if ($type == 'php') {
            $ret = serialize($val);
        } else {
            $ret = $val;
        }
        return $ret;
    }

    private static function decode($val, $type='') {
        $ret = '';
        if ($type == 'json') {
            $ret = json_decode($val, true);
        } else if ($type == 'php') {
            $ret = unserialize($val);
        } else {
            $ret = $val;
        }
        return $ret;
    }
}
