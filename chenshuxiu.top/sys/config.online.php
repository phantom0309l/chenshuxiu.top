<?php
$domain = "fangcunyisheng.com";
include dirname(__FILE__) . "/_config.php";

$config['websocket_host'] = "wss://websocket." . $domain;
$config['websocket_port'] = "9502";

$config['websocket_service_host'] = "127.0.0.1";
$config['websocket_service_port'] = "9502";

$config['websocket_http_host'] = "10.172.220.86";
$config['websocket_http_port'] = "9503";