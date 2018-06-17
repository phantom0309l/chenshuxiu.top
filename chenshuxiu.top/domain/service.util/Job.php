<?php

class Job
{

    private static $instance = null;

    private $client;

    private $url;

    private function __construct() {
        $nsqdConfig = Config::getConfig('nsqd');
        $nsqdServerHost = $nsqdConfig['host'];
        $nsqdServerPort = $nsqdConfig['port'];

        $this->url = "http://" . $nsqdServerHost . ":" . $nsqdServerPort;
    }

    private function __clone() {}

    public static function getInstance($needNew = false) {
        if (self::$instance === null || $needNew === true) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function doBackground($topic, $data) { // 异步工作
        if (! $topic) {
            return;
        }
        //$uri = "http://fangcundev:4151/pub?topic=monitormsg";
        $url = $this->url . "/pub?topic=". $topic;
        $ret = FUtil::curlPost($url, $data);
        if ($ret != 'OK') {
            Debug::warn(__METHOD__ . " failed ret:$ret");
        }
    }
}
