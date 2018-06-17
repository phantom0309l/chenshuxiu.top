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

$unitofwork = BeanFinder::get("UnitOfWork");

function sendmsg ($wxuser) {
    // 得到模板内容
    if ($wxuser instanceof WxUser) {
        $str = "行为训练管理员";
        $content = "『课堂贴士』更新了-- <<据说看过这篇文章后，90%家长都会和孩子谈心了>>";
        $openid = $wxuser->openid;

        $first = array(
            "value" => "",
            "color" => "");
        $keywords = array(
            array(
                "value" => $str,
                "color" => "#aaa"),
            array(
                "value" => $content,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);
        $url = "http://wx.fangcunyisheng.com/sfbt/one?lessonid=102040509&openid={$openid}";

        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
    }
}

$cond = "AND wxshopid=3 AND subscribe = 1";
$wxusers = Dao::getEntityListByCond("WxUser", $cond);
foreach ($wxusers as $i => $a) {
    sendmsg($a);
    echo "\n====[{$a->id}][{$i}]===\n";
}

$unitofwork->commitAndInit();
