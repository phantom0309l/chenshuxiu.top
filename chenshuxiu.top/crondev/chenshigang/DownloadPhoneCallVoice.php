<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$sql = "SELECT id, callsid,recordurl,filename FROM meetings WHERE recordurl<>'' ORDER BY id DESC LIMIT 1000";
$rows = Dao::queryRows($sql);

$gearmanConfig = Config::getConfig('gearman');
$jobServerHost = $gearmanConfig['host'];
$jobServerPort = $gearmanConfig['port'];
$client = new GearmanClient();
$client->addServer($jobServerHost, $jobServerPort);
foreach ($rows as $row) {
    $paramArr = array(
        'appid' => $row['appid'],
        'callsid' => $row['callsid'],
        'recordurl' => $row['recordurl']
    );
    $params = json_encode($paramArr);
    $result = $client->doBackground('download_meeting_airvoice', $params); // 异步进行，只返回处理句柄。
}

Debug::flushXworklog();
