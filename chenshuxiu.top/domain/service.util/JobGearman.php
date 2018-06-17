<?php

class JobGearman
{

    private static $instance = null;

    private $client;

    private function __construct() {
        $gearmanConfig = Config::getConfig('gearman');
        $jobServerHost = $gearmanConfig['host'];
        $jobServerPort = $gearmanConfig['port'];
        $this->client = new GearmanClient();
        $this->client->addServer($jobServerHost, $jobServerPort);
    }

    private function __clone() {}

    public static function getInstance($needNew = false) {
        if (self::$instance === null || $needNew === true) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function doBackground($funcName, $data) { // 异步工作
        if (! $funcName) {
            return;
        }
        $result = $this->client->doBackground($funcName, $data); // 异步进行，只返回处理句柄。
    }
}
