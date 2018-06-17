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


$media_id = "FuPvI2NhR8CwX5toX_FU3ddQ4aaewr6O_1JTR0WOeGO5EcH9Ez8upjqm4iM2kw0O";
$cond = ' AND createtime > "2018-05-16" AND  createtime < "2018-05-17"';
$list = Dao::getEntityListByCond('WxPicMsg', $cond);
foreach ($list as $one) {
    $url = getUrl($one);
    echo $url, "\n";
    $picture = Picture::createByFetchWX($url);
    if (!$picture) {
        echo "picture is null\n";
    } else {
        echo $picture->id, "\n";
    }
}

function getMediaId($url) {
    $mediaId = explode("&media_id=", $url)[1];
    return $mediaId;
}

function getUrl($wxpicmsg) {
    $wxshop = $wxpicmsg->wxuser->wxshop;
    $access_token = $wxshop->getAccessToken();
    $media_id = $wxpicmsg->media_id;
    $picurl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;
    return $picurl;
}
