<?php
$domain = "fangcunhulian.cn";
include dirname(__FILE__) . "/_config.php";

$config['websocket_host'] = "wss://websocket.fangcunhulian.cn";
$config['websocket_port'] = "9502";

$config['websocket_service_host'] = "127.0.0.1";
$config['websocket_service_port'] = "9502";

$config['websocket_http_host'] = "10.25.179.16";
$config['websocket_http_port'] = "9503";

//$config['photo_uri'] = "https://photo.{$domain}";
$config['photo_uri'] = "https://photo.fangcunyisheng.com";
$config['voice_uri'] = "https://voice.fangcunyisheng.com";
$config['upload_uri'] = "http://fangcundata:9096/upload";
$config['img_uri'] = "https://img.{$domain}";
// $config['photo_uri'] = "http://photo.fangcunyisheng.com";

$config['icp'] = isset($_COOKIE['dev_user']) ? 'dev_user=' . $_COOKIE['dev_user'] : "icp";
$config['company'] = '方寸全球技术研发中心-开发环境';

$config['xworkdbOpen'] = true;
$config['log_level'] = LogLevel::LEVEL_TRACE;
