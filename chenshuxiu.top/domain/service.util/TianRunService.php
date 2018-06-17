<?php
/**
 * Created by PhpStorm.
 * User: qiaoxiaojin
 * Date: 18/4/27
 * Time: 下午10:37
 */

class TianRunService
{
    //host
    const HOST = "http://api.clink.cn";

    //获取外呼通话记录
    public static function getCdrObList($startTime = "", $endTime = "", $mobile = "") {
        $host = self::HOST;
        $url = "{$host}/interfaceAction/cdrObInterface!listCdrOb.action";
        return self::getCdrIbListOrObListImp($url, $startTime, $endTime, $mobile);
    }

    //获取来电通话记录
    public static function getCdrIbList($startTime = "", $endTime = "", $mobile = "") {
        $host = self::HOST;
        $url = "{$host}/interfaceAction/cdrObInterface!listCdrIb.action";
        return self::getCdrIbListOrObListImp($url, $startTime, $endTime, $mobile);
    }

    //获取录音文件文本
    public static function asrDownload($recordFileName) {
        $host = self::HOST;
        $url = "{$host}/interfaceAction/asr!download.action";

        $baseRequestData = self::getBaseRequestData();
        $data = [];
        $data["recordFileName"] = $recordFileName;
        $data["side"] = "all";
        $data += $baseRequestData;

        return FUtil::curlPost($url, $data);
    }

    //获取来电通话记录 外呼记录 实现
    private static function getCdrIbListOrObListImp($url, $startTime = "", $endTime = "", $mobile = "") {
        $baseRequestData = self::getBaseRequestData();
        $data = [];
        if ($startTime) {
            $data["startTime"] = $startTime;
        }
        if ($endTime) {
            $data["endTime"] = $endTime;
        }
        if ($mobile) {
            $data["title"] = "customer_number";
            $data["value"] = urlencode($mobile);
        }
        $data += $baseRequestData;
        return FUtil::curlPost($url, $data);
    }

    private static function getBaseRequestData() {
        $data = [];
        $data["enterpriseId"] = Config::getConfig('cdr_enterpriseid');
        $data["userName"] = Config::getConfig('cdr_userame');

        $seed = time();
        $data["seed"] = $seed;
        $data["pwd"] = self::getPWD($seed);
        return $data;
    }

    private static function getPWD($seed = "") {
        $pwd = Config::getConfig('cdr_pwd');
        $pwdonemd5 = md5($pwd);
        $pwdtwomd5 = md5($pwdonemd5 . $seed);
        return $pwdtwomd5;
    }
}