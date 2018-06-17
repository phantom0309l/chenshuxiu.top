<?php
$domain = "fangcunhulian.cn";
include dirname(__FILE__) . "/_config.txj.php";

$config['icp'] = isset($_COOKIE['dev_user']) ? 'dev_user=' . $_COOKIE['dev_user'] : "icp";
$config['company'] = '方寸全球技术研发中心-开发环境';

$config['xworkdbOpen'] = false;
$config['log_level'] = LogLevel::LEVEL_TRACE;
