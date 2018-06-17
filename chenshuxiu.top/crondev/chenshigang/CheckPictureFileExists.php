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


$offset = 0;
$sql = "SELECT id, createtime, picname, picext, fromurl FROM pictures WHERE id > $offset LIMIT 1000";
$pictures = Dao::queryRows($sql);
$i = 0;
while ($pictures) {
    //echo $i, "\t", $offset, "\n";
    foreach ($pictures as $picture) {
        if (!$picture['picname'] || !$picture['picext']) {
            continue;
        }
        $filePath = "/home/xdata/xphoto/" . $picture['picname'] . "." . $picture['picext'];
        if (!file_exists($filePath)) {
            echo $picture['id'], "\t", $picture['createtime'], "\t", $filePath, "\t", $picture['fromurl'], "\n";
        }
    }
    $offset = $pictures[count($pictures) - 1]['id'];
    $sql = "SELECT id, createtime, picname, picext, fromurl FROM pictures WHERE id > $offset LIMIT 1000";
    $pictures = Dao::queryRows($sql);
    $i += 1000;
}

