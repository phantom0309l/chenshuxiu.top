<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/6/14
 * Time: 18:21
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";
//$unitofwork = BeanFinder::get("UnitOfWork");
//$unitofwork->commitAndInit();

$wxshop = WxShop::getById(2);
$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$wxshop->access_token}";

$fields = array(
    "action_name" => "QR_LIMIT_STR_SCENE",
    "action_info" => array(
        "scene" => array(
            "scene_str" => 'test'
        )
    )
);

$fields = urldecode(json_encode($fields));
$jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
echo $jsonStr;
$json = json_decode($jsonStr, true);

echo "\n";
echo $json['ticket'];
echo "\n";
echo "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $json['ticket'];
echo "\n";