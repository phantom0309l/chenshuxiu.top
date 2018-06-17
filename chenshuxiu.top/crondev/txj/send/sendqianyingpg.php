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

function sendmsg ($id) {
    $wxuser = WxUser::getById( $id );
    // 得到模板内容
    if ($wxuser instanceof WxUser) {
        $wx_uri = Config::getConfig("wx_uri");
        $str = "医生助理";
        $content = "请填写SNAP-IV量表，brif量表，weiss量表，conner量表，青少年生活事件量表，以便完善孩子信息。";
        $openid = $wxuser->openid;

        $first = array(
            "value" => "",
            "color" => "#ff6600");
        $keywords = array(
            array(
                "value" => $str,
                "color" => "#aaa"),
            array(
                "value" => $content,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        $url = "{$wx_uri}/game/scaleindex?openid={$openid}";

        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
    }
}

//$sql = "select id from wxusers where subscribe = 1 and wxshopid=3 and userid=10004";

$sql = 'select c.id from patients a
inner join users b on b.patientid = a.id
inner join wxusers c on c.userid = b.id
 where a.name in ("马雨薇","左祖铭","魏伯宇","徐天瑞") and a.status=1 and a.subscribe_cnt>0 and c.wxshopid=3 and c.subscribe = 1 group by a.id';
$ids = Dao::queryValues($sql);
foreach ($ids as $i => $id) {
    sendmsg($id);
    echo "\n====[{$id}][{$i}]===\n";
}

$unitofwork->commitAndInit();
