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

$unitofwork = BeanFinder::get("UnitOfWork");

$num = 0;

$openid_list = array();

$sql1 = "select * from wxusers where ref_objtype='' and wxshopid=1 and userid=0";
// $sql1 = "select * from wxusers where id=97";
$wxusers1 = Dao::loadEntityList("WxUser", $sql1);

$sql2 = "select * from wxusers a inner join users b on a.userid = b.id where b.patientid=0 and a.ref_objtype='' and a.wxshopid=1";
$wxusers2 = Dao::loadEntityList("WxUser", $sql2);

$title = "“别人家妈妈”喊你一起来上免费训练课《十周提升注意力》，在家就能上喔！快来参加吧！";
$url = "http://mp.weixin.qq.com/s?__biz=MzI3NjA3MjU4NA==&mid=415883739&idx=1&sn=6cedca895135d32b53feea5bec29bc39#rd";

foreach ($wxusers1 as $wxuser) {
    // 发送模板消息
    $content = array(
        "keyword1" => array(
            "value" => "医生助理",
            "color" => "#173177"),  //
        "keyword2" => array(
            "value" => $title,
            "color" => "#173177"))    //
    ;

    $content = json_encode($content);

    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);

    $num ++;

    Debug::trace("=====[$wxuser->id][][$num]=====");

}

$unitofwork->commitAndInit();

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
