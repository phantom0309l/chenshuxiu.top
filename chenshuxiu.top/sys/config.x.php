<?php
$domain = "fangcunyisheng.com";
include dirname(__FILE__) . "/_config.php";

$config['icp'] = isset($_COOKIE['dev_user']) ? 'dev_user=' . $_COOKIE['dev_user'] : "icp";
$config['company'] = '方寸全球技术研发中心-生产环境';
