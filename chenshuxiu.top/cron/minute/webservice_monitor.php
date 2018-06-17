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

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][webservice_monitor.php]=====");

$url = Config::getConfig("www_uri");

$content = XHttpRequest::curl_getUrlContents($url);
$keywords = array(
    '方寸医生',
    '免责声明');
$result = true;

foreach ($keywords as $a) {
    $pos = mb_strpos($content, $a);
    if ($pos < 1) {
        $result = false;
        break;
    }
}
if ($result) {
    echo "\n-----end----- " . XDateTime::now();
    return;
}

$content = array(
    "first" => array(
        "value" => "系统出错: 新首页出错",
        "color" => "#000000"),
    "keyword1" => array(
        "value" => "www前台",
        "color" => "#173177"),
    "keyword2" => array(
        "value" => date("Y-m-d H:i:s"),
        "color" => "#173177"),
    "keyword3" => array(
        "value" => "验证",
        "color" => "#FF0000"),
    "remark" => array(
        "value" => "请尽快处理。点击(详情)进入。",
        "color" => "#000000"));

$unitofwork = BeanFinder::get("UnitOfWork");

$content = json_encode($content, JSON_UNESCAPED_UNICODE);
$url = UrlFor::wwwIndex();

// 设置一个开关,发给技术的opstxt,不入 pipe
XContext::setValue("sendOpsTxtMessage", true);

// 方寸研发，所有监控人员都关注方寸研发，统一给方寸研发发消息
$wxusers = WxUserDao::getListByWxshopid_Ops(8);
foreach ($wxusers as $wxuser) {
    if ($wxuser instanceof WxUser) {
        $pushMsg = PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "followupNotice", $content, $url);
        if ($pushMsg instanceof PushMsg) {
            $pushMsg->is_monitor_msg = 1; // 标记为监控消息
        }
    }
}

XContext::setValue("sendOpsTxtMessage", false);

$unitofwork->commitAndInit();

Debug::trace("=====[cron][end][webservice_monitor.php]=====");
// Debug::flushXworklog(); // 不记日志

echo "\n-----end----- " . XDateTime::now();
