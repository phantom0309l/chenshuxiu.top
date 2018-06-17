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

class Lilly_md5
{

    public function dopush () {
        $app_id = "lilly";
        $app_secret = "76ae6d63b449f379";
        $doctor_code = "R1+A+2nbZk7qh06lJ1dn/w==";
        $timestamp = "1496332411";
        $current_signature = "c45f0907823174f9033fcba23f41d7f3";

        $signature = md5("[{$app_id}][{$app_secret}][{$doctor_code}][{$timestamp}]");
        echo "\n\n-----signature----- " . $signature;

        if($current_signature == $signature){
            echo "\n\n-----ok----- ";
        }
    }

}
// //////////////////////////////////////////////////////

$process = new Lilly_md5(__FILE__);
$cnt = $process->dopush();
