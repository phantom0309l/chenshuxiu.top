<?php

class MsgProxy
{

    public static $uri = "http://localhost:8848";

    public static function sendmsg_sms ($phone, $content, $pushmsgid) {
        $target_url = self::$uri . "/sendmsg/sms?phone={$phone}&receptorid={$pushmsgid}";

        return (bool) (XHttpRequest::curl_postUrlContents($target_url, $content) == "fine");
    }

    public static function sendmsg_wechat ($openid, $content, $phone, $pushmsgid) {
        $target_url = self::$uri . "/sendmsg/wechat?openid={$openid}&receptorid={$pushmsgid}" . ($phone ? "&phone={$phone}" : "");
        return (bool) (XHttpRequest::curl_postUrlContents($target_url, $content) == "fine");
    }

    public static function sendtemplate_wechat ($openid, $templateid, $content, $pushmsgid, $url) {
        $target_url = self::$uri . "/sendtemplate/wechat?openid={$openid}&templateid={$templateid}&receptorid={$pushmsgid}" . ($url ? "&url=$url" : "");
        return (bool) (XHttpRequest::curl_postUrlContents($target_url, $content) == "fine");
    }

}
