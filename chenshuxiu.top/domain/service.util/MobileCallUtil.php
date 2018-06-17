<?php

class MobileCallUtil
{

    // 子帐号
    public static $subAccountSid = '2f4f5ba7936b11e5bb61ac853d9d52fd';

    // 子帐号Token
    public static $subAccountToken = 'edee7c3215213f57acf35bbc2e7ae1df';

    // VoIP帐号
    public static $voIPAccount = '8005712900000003';

    // VoIP密码
    public static $voIPPassword = 'BuwZf3oF';

    // 应用Id
    public static $appId = '8a48b5515124598801513769082d3a6e';

    // 请求地址，格式如下，不需要写https://
    public static $serverIP = 'app.cloopen.com';

    // 请求端口
    public static $serverPort = '8883';

    // REST版本号
    public static $softVersion = '2013-12-26';

    // return array ('callsid' => 'callsid', 'datecreated' => 'datecreated')
    public static function callBack ($to, $from = "01082038177", $customerSerNum = "01082038177", $fromSerNum = "", $promptTone = "", $alwaysPlay = "", $terminalDtmf = "", $userData = "",
            $maxCallTime = "", $hangupCdrUrl = "", $needBothCdr = 0, $needRecord = 1, $countDownTime = "", $countDownPrompt = "") {
        // 初始化REST SDK
        $rest = new CcpREST(self::$serverIP, self::$serverPort, self::$softVersion);
        $rest->setSubAccount(self::$subAccountSid, self::$subAccountToken, self::$voIPAccount, self::$voIPPassword);
        $rest->setAppId(self::$appId);

        // 调用回拨接口
        // echo "Try to make a callback,called is $to <br/>";
        $result = $rest->callBack($from, $to, $customerSerNum, $fromSerNum, $promptTone, $alwaysPlay, $terminalDtmf, $userData, $maxCallTime, $hangupCdrUrl,
                $needBothCdr, $needRecord, $countDownTime, $countDownPrompt);
        Debug::trace(__METHOD__ . " callBack from:$from to:$to result:" . json_encode($result, JSON_UNESCAPED_UNICODE));
        if ($result == NULL) {
            Debug::warn(__METHOD__ . " callBack from:$from to:$to result is null");
            return false;
        }
        if ($result->statusCode != 0) {
            Debug::warn(__METHOD__ . " callBack from:$from to:$to result:" . json_encode($result, JSON_UNESCAPED_UNICODE));
            return false;
        }
        return (array) $result->CallBack;
        /*
         * if($result == NULL ) { echo "result error!"; break; }
         * if($result->statusCode!=0) { echo "error code :" .
         * $result->statusCode . "<br>"; echo "error msg :" . $result->statusMsg
         * . "<br>"; //TODO 添加错误处理逻辑 } else { echo "callback success!<br>"; //
         * 获取返回信息 $callback = $result->CallBack; echo
         * "callSid:".$callback->callSid."<br/>"; echo
         * "dateCreated:".$callback->dateCreated."<br/>"; //TODO 添加成功处理逻辑 }
         */
    }

}
