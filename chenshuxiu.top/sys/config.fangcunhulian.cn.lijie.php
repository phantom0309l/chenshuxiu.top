<?php
$domain = "fangcunhulian.cn";
include dirname(__FILE__) . "/_config.lijie.php";

$config['websocket_host'] = "101.201.110.150 ";
$config['websocket_port'] = "9502";

$config['photo_uri'] = "http://photo.{$domain}";

$config['icp'] = isset($_COOKIE['dev_user']) ? 'dev_user=' . $_COOKIE['dev_user'] : "icp";
$config['company'] = '方寸全球技术研发中心-开发环境';

$config['xworkdbOpen'] = false;
$config['log_level'] = LogLevel::LEVEL_TRACE;
