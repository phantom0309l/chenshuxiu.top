<?php

class UrlFor
{

    public static function jump301 ($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        echo date("Y-m-d h:i:s"); // nginx反向代理有个bug ，所以不得不输出点东西
        exit();
    }

    public static function jump302 ($url) {
        Debug::trace("UrlFor::jump302: {$url} ");

        header("HTTP/1.1 302 Moved Temporarily");
        header("Location: $url");
        echo date("Y-m-d h:i:s"); // nginx反向代理有个bug ，所以不得不输出点东西
        exit();
    }

    // pc网站登录
    public static function wwwLogin ($redirect_url = '') {
        $url = Config::getConfig("www_uri") . "/login/login";

        if ($redirect_url) {
            $url .= "?redirect_url=" . urlencode($redirect_url);
        }

        return $url;
    }

    // pc网站登录
    public static function wwwLogout () {
        return Config::getConfig("www_uri") . "/login/logout";
    }

    // 医生h5网站登录
    public static function dmLogin ($redirect_url = '') {
        $url = Config::getConfig("dm_uri") . "/login/login";
        if ($redirect_url) {
            $url .= "?redirect_url=" . urlencode($redirect_url);
        }

        return $url;
    }

    // 医生h5网站登录
    public static function dmLogout () {
        return Config::getConfig("dm_uri") . "/login/logout";
    }

    // 网站首页
    public static function wwwIndex () {
        return Config::getConfig("www_uri") . "/";
    }

    // 医生pc后台首页
    public static function doctorIndex () {
        return Config::getConfig("doctor_uri") . "/";
    }

    // new医生后台首页
    public static function adminIndex () {
        return Config::getConfig("admin_uri") . "/";
    }

    // 运营后台首页
    public static function auditIndex () {
        return Config::getConfig("audit_uri") . "/";
    }

    // 随访中心后台首页
    public static function suifangIndex () {
        return Config::getConfig("suifang_uri") . "/";
    }

    // 医生h5后台首页
    public static function dmIndex () {
        return Config::getConfig("dm_uri") . "/";
    }

    // 患者首页 单个页面
    public static function dmAppPatient ($patientid = 0) {
        return self::dmAppChart($patientid);
    }

    // 患者数据分析页 单个页面
    public static function dmAppChart ($patientid = 0) {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/app/chart?patientid={$patientid}&token={$token}";
    }

    // 流列表 翻页 缓存第一页 有ajax
    public static function dmAppPipes ($patientid = 0) {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/app/pipes?patientid={$patientid}&token={$token}";
    }

    // 患者评估列表 单个页面 不翻页 无ajax
    public static function dmAppAnswerSheets ($patientid = 0) {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/app/answersheets?patientid={$patientid}&token={$token}";
    }

    // 患者评估详情 多个独立页面
    public static function dmAppAnswerSheet ($answersheetid = 0) {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/app/answersheet?answersheetid={$answersheetid}&token={$token}";
    }

    // 门诊时间设置
    public static function dmAppSchedule () {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/schedulemgr/list?token={$token}";
    }

    //
    public static function dmAppTasks () {
        $token = XContext::getValueEx("token", '');
        return Config::getConfig("dm_uri") . "/app/tasks?token={$token}";
    }

    // 患者h5后台首页
    public static function wxIndex () {
        return Config::getConfig("wx_uri") . "/";
    }

    public static function wxMy ($openid = "") {
        return Config::getConfig("wx_uri") . "/fbtmy/overview?openid={$openid}";
    }
}
