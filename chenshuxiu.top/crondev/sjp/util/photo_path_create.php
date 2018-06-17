<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$pathBase = "/home/xdata/xphoto";

$arr = array(
    0,
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    a,
    b,
    c,
    d,
    e,
    f);

foreach ($arr as $a) {
    foreach ($arr as $b) {
        foreach ($arr as $c) {
            $dir = $pathBase . "/{$a}/{$b}{$c}/";

            echo "\n" . $dir;

            // 检测子目录是否存在;
            if (! file_exists($dir)) {
                mkdir($dir, 0777, true); // 不存在则创建;
                echo "+";
            } else {
                ".";
            }
        }
    }
}
