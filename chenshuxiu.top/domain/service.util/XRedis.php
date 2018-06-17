<?php
/*
 * @desc 此类目前只支持单个redis，以后扩展到redis集群需要重新编写此类
 */
class XRedis
{

    protected static $conn = null;

    protected function __construct () {}

    protected static function connect ($host, $port, $pconnect, $auth) {
        // redis conn 失败会抛异常
        // 尝试三次连接
        $conn = null;
        $retry = 3;
        $r = null;
        $timeout = 10;
        for ($i = 0; $i < $retry; $i++) {
            $conn = new Redis();
            if ($pconnect) {
                $r = $conn->pconnect($host, $port, $timeout);
            } else {
                $r = $conn->connect($host, $port, $timeout);
            }
            if ($r) {
                break;
            } else {
                Debug::warn(__METHOD__ . ' retry connect retry times ' . ($i + 1));
                usleep(2000); // 2毫秒
            }
        }
        DBC::requireNotEmpty($r, 'redis 连接失败');
        $conn->auth($auth);
        return $conn;
    }

    /*
     * @desc 获取redis连接 
     * @return resource id or false
     */
    public static function getConnect () {
        if (self::$conn) {
            return self::$conn;
        }
        $config = Config::getConfig('redis', array());
        if (! $config || ! is_array($config)) {
            // need log
            Debug::warn(__METHOD__ . ' config is empty');
            return false;
        }

        $host = $config['host'] ?? '';
        $port = $config['port'] ?? '';
        $timeout = $config['timeout'] ?? 0;
        $pconnect = isset($config['pconnect']) ? ! ! $config['pconnect'] : false;
        $auth = $config['auth'] ?? '';

        if (! $host || ! $port) {
            // need log
            Debug::warn(__METHOD__ . ' host or port is empty');
            return false;
        }

        self::$conn = self::connect($host, $port, $pconnect, $auth);
        return self::$conn;
    }

    /*
     * @desc 重新链接redis
     */
    public static function reconnect () {
        self::$conn = null;
        return self::getConnect();
    }

    public function __destruct() {
        Debug::trace('Redis connection closed');
        self::$conn->close();
    }
}
