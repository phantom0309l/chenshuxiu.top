<?php

/**
 * XMemCached
 * @desc		MemCached封装类
 * @remark		依赖类: Debug , Config
 * @copyright	(c)2012 xwork .
 * @file		XMemCached.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class XMemCached
{
    // 当memcache注册失败时,返回NullMemCached
    public static function getOneInstance () {
        $m = new XMemCached();
        if ($m->set("test-server-status", "ok")) {
            Debug::sys("[-- XMemCached start success. --]");
            return $m;
        } else {
            Debug::sys("[-- XMemCached start false! set cacheOpen=false. --]");
            Config::setConfig("cacheOpen", false);
            return new NullMemCached();
        }
    }

    private $memCache;

    public function getInnerMemCache () {
        return $this->memCache;
    }

    public function __construct () {
        $this->memCache = new Memcache();
        $memCachedConfigs = Config::getConfig("mem_cached_cluster");
        foreach ($memCachedConfigs as $memCachedConfig) {
            $str = "[-- XMemCached addServer " . $memCachedConfig['host'] . ':' . $memCachedConfig['port']." --]";
            Debug::sys($str);
            @$this->memCache->addServer($memCachedConfig['host'], $memCachedConfig['port']);
        }
    }

    public function delete ($key) {
        // 直接判断memcache里有没有值,不走hasEntity
        $ret = @$this->memCache->get($key);
        if (! empty($ret)) {
            Debug::sys("[-- XMemCached delete of $key --]");
            return @$this->memCache->delete($key, 0);
        } else
            return true;
    }

    public function read ($key) {
        // $str = "[XMemCached::read][beg][{$key}]--";
        // Debug::sys($str);
        // $xmemcachelog = Config::getConfig("xmemcachelog",false);
        // Config::setConfig("xmemcachelog",true);

        $cacheNeedReload = Config::getConfig("cacheNeedReload", false);
        Config::setConfig("cacheNeedReload", false);

        $ret = $this->get($key);

        Config::setConfig("cacheNeedReload", $cacheNeedReload);

        $hit = 0;
        if ($ret)
            $hit = 1;

            // Config::setConfig("xmemcachelog",$xmemcachelog);
        $str = "[XMemCached::read] {$hit} of {$key}--";
        Debug::sys($str);
        return $ret;
    }

    public function write ($key, $value, $compression = false, $expireTime = 86400) {
        Debug::sys("[XMemCached::write][{$key}] --");
        // $xmemcachelog = Config::getConfig("xmemcachelog",false);
        // Config::setConfig("xmemcachelog",true);

        $this->set($key, $value, $compression, $expireTime);

        // Config::setConfig("xmemcachelog",$xmemcachelog);
        // Debug::sys("[XMemCached::write][end] --");
    }

    public function get ($key) {
        $cacheNeedReload = Config::getConfig("cacheNeedReload", false);
        if ($cacheNeedReload) {
            Debug::sys("[-- cacheNeedReload : XMemCached get 0 of $key --]");
            return null;
        }

        $ret = @$this->memCache->get($key);

        // 0,null,empty,"",array() 都认为没取到
        if (empty($ret)) {
            Debug::sys("[-- XMemCached get 0 of $key --]");
            $ret = null;
        } else {
            Debug::sys("[-- XMemCached get 1 of $key --]");
        }
        return $ret;
    }

    public function set ($key, $value, $compression = false, $expireTime = 600) {
        DBC::requireNotEmptyString($key, 'memcache key cannot empty');
        DBC::requireNotNull($value, 'memcache value cannot null');

        // 直接判断memcache,不走hasEntity
        $ret = @$this->memCache->get($key);

        if (! empty($ret)) {
            return @$this->replace($key, $value, $compression, $expireTime);
        } else {
            Debug::sys("[-- XMemCached set of $key --]");
            return @$this->memCache->set($key, $value, $compression, $expireTime);
        }
    }

    public function replace ($key, $value, $compression = false, $expireTime = 600) {
        Debug::sys("[-- XMemCached replace of $key --]");
        return @$this->memCache->replace($key, $value, $compression, $expireTime);
    }

    public function flush () {
        return @$this->flush();
    }

    public function hasEntity ($key) {
        $value = $this->get($key);
        return (empty($value) == false);
    }

    // 获取一个锁
    public function getLock ($key, $timeout = 60) {
        $key = self::genKey("mlock_" . $key);

        $waitime = 500;
        $totalWaitime = 0;
        $time = $timeout * 1000000;
        while ($totalWaitime < $time && false == @$this->memCache->add($key, 1, false, $timeout)) {
            usleep($waitime);
            $totalWaitime += $waitime;
        }

        if ($totalWaitime >= $time)
            throw new Exception('can not get memcached lock for waiting ' . $timeout . 's.');
    }

    // 释放锁
    public function releaseLock ($key) {
        $key = self::genKey("mlock_" . $key);
        @$this->memCache->delete($key);
    }

    public static function getStatss () {
        $statss = array();

        $memCachedConfigs = Config::getConfig("mem_cached_cluster");
        foreach ($memCachedConfigs as $memCachedConfig) {
            $memcache_obj = new Memcache();
            @$memcache_obj->connect($memCachedConfig['host'], $memCachedConfig['port']);
            $statss[$memCachedConfig['host']] = @$memcache_obj->getStats();
        }

        return $statss;
    }

    public static function flushServers () {
        Debug::sys("[-- XMemCached::flushServers() --]");
        $memCachedConfigs = Config::getConfig("mem_cached_cluster");
        foreach ($memCachedConfigs as $memCachedConfig) {
            $memcache_obj = new Memcache();
            @$memcache_obj->connect($memCachedConfig['host'], $memCachedConfig['port']);
            @$memcache_obj->flush();
        }
    }

    // 产生key,供绕过UnitOfWork直接调用
    public static function genKey ($key) {
        return Config::getConfig("key_prefix") . '_fix_' . $key;
    }

    // setCache,供绕过UnitOfWork直接调用,慎用,至少需要另外一工程师评审; $cacheKey 例子如下:
    // $cacheHour = date("H");
    // $cacheKey = "DealAction::doIndex [$cacheHour] =
    // [{$city->id},$type,$priceGrade,$orderSort,$orderSortAD,$keyword,$page]";
    public static function setCache ($key, $value, $compression = false, $expireTime = 4000) {
        // 没有开启则直接返回true
        if (false == Config::getConfig("cacheOpen")) {
            Debug::sys("[-- NullMemCached setCache 0 of {$key} --]");
            return true;
        }

        $key = self::genKey($key);
        if (empty($value)) {
            Debug::sys("[-- XMemCached setCache empty of {$key} --]");
            return true;
        }

        Debug::sys("[-- XMemCached setCache 1 , time={$expireTime} of {$key} --]");
        $xMemCached = BeanFinder::get("XMemCached");
        return $xMemCached->set($key, $value, $compression, $expireTime);
    }

    // getCache,供绕过UnitOfWork直接调用,慎用
    public static function getCache ($key) {
        // 没有开启则直接返回true
        if (false == Config::getConfig("cacheOpen")) {
            Debug::sys("[-- NullMemCached getCache 0 of {$key} --]");
            return false;
        }

        $key = self::genKey($key);
        $xMemCached = BeanFinder::get("XMemCached");
        $value = $xMemCached->get($key);

        if (empty($value)) {
            Debug::sys("[-- XMemCached getCache 0 of {$key} --]");
        } else {
            Debug::sys("[-- XMemCached getCache 1 of {$key} --]");
        }

        return $value;
    }
}

class NullMemCached
{

    private $memCache = null;

    public function __construct () {}

    public function delete ($key) {
        return true;
    }

    public function read ($key) {
        return null;
    }

    public function write ($key, $value, $compression = false, $expireTime = 30) {
        return true;
    }

    public function get ($key) {
        return null;
    }

    public function set ($key, $value, $compression = false, $expireTime = 30) {
        return true;
    }

    public function replace ($key, $value, $compression = false, $expireTime = 30) {
        return true;
    }

    public function flush () {
        return true;
    }

    public function hasEntity ($key) {
        return false;
    }

    public static function flushServers () {
        return true;
    }

    // 获取一个锁
    public function getLock ($key, $timeout = 60) {}

    // 释放锁
    public function releaseLock ($key) {}
}

/*
 * Memcache 函数说明

Memcache::add — 添加一个值，如果已经存在，则返回false
Memcache::addServer — 添加一个可供使用的服务器地址
Memcache::close — 关闭一个Memcache对象
Memcache::connect — 创建一个Memcache对象
memcache_debug — 控制调试功能
Memcache::decrement — 对保存的某个key中的值进行减法操作
Memcache::delete — 删除一个key值
Memcache::flush — 清除所有缓存的数据
Memcache::get — 获取一个key值
Memcache::getExtendedStats — 获取进程池中所有进程的运行系统统计
Memcache::getServerStatus — 获取运行服务器的参数
Memcache::getStats — 返回服务器的一些运行统计信息
Memcache::getVersion — 返回运行的Memcache的版本信息
Memcache::increment — 对保存的某个key中的值进行加法操作
Memcache::pconnect — 创建一个Memcache的持久连接对象
Memcache::replace — R对一个已有的key进行覆写操作
Memcache::set — 添加一个值，如果已经存在，则覆写
Memcache::setCompressThreshold — 对大于某一大小的数据进行压缩
Memcache::setServerParams — 在运行时修改服务器的参数

建议用面向对象的方式来测试这个库：

$memcache = new Memcache;
$memcache->connect('localhost', 11211) or die ("Could not connect");
$version = $memcache->getVersion();
echo "Server's version: ".$version."<br/>\n";
*/
