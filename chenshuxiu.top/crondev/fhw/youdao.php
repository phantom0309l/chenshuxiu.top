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

class Test
{
    public function dowork()
    {
        $word = $argv[1];

        print_r($word);

        $durl = "http://fanyi.youdao.com/openapi.do?keyfrom=fangcunhulian&key=2042553599&type=data&doctype=json&version=1.1&q={$word}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $r = curl_exec($ch);
        curl_close($ch);

        $arr = json_decode($r, true);

        $result = $arr['basic']['explains'];

        $resultStr = implode("\n", $result);

        print_r($resultStr);

        echo "\n";
    }
}

$test = new Test();
$test->dowork();
