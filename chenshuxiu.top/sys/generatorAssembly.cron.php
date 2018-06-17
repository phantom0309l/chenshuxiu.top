<?php
include_once 'PathDefine.php';
include_once (ROOT_TOP_PATH . "/../core/xutil/GeneratorAssemblyProcess.class.php");

$assemblyFileName = ROOT_TOP_PATH . "/cron/Assembly.php";

$includepaths = array();
$includepaths[] = ROOT_TOP_PATH . "/../core";
$includepaths[] = ROOT_TOP_PATH . "/domain";
$includepaths[] = ROOT_TOP_PATH . "/cron";

$notincludepaths = array();
$notincludepaths[] = ROOT_TOP_PATH . "/../core/util/simpletest";
$notincludepaths[] = ROOT_TOP_PATH . "/../core/tools";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/WxpayAPI_php_v3";
$notincludepaths[] = ROOT_TOP_PATH . "/cron/bak";
$notincludepaths[] = ROOT_TOP_PATH . "/cron/db2entity";

$process = new GeneratorAssemblyProcess($assemblyFileName, $includepaths, $notincludepaths);
$process->dowork();
