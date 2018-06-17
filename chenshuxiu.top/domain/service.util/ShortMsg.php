<?php

class ShortMsg extends MsgBase
{

    public static function sendmsg_asyn ($userid, $patientid, $content, $appendarr = array()) {
        $row = array(
            "userid" => $userid,
            "patientid" => $patientid,
            "sendway" => "sms",
            "content" => $content);
        $row += $appendarr;
        $row += parent::$defaultsrcarr;
        $pushMsg = PushMsg::createByBiz($row);
        return $pushMsg;
    }

    /**
     * 发送模板短信
     *
     * @param $to   手机号码集合,用英文逗号分开
     * @param $datas    内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param $tempId   模板Id
     * @return mixed|SimpleXMLElement
     */
    public static function sendTemplateSMS_j4now ($to, $datas, $tempId) {
        $accountSid = 'aaf98f89512446e201513765ded03941';
        $accountToken = '9079a8326d524b00abfb2fa9c8af295b';
        $appId = '8a48b5515124598801513769082d3a6e';
        //沙盒环境
        //$serverIP = 'sandboxapp.cloopen.com';
        //正式环境
        $serverIP = 'app.cloopen.com';
        $serverPort = '8883';
        $softVersion = '2013-12-26';
        // lobal
        // $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        // 初始化REST SDK
        $rest = new CcpREST($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        $result = $rest->sendTemplateSMS($to, $datas, $tempId);
        /*
         * if($result == NULL ) { echo "result error!"; break; }
         * if($result->statusCode!=0) { echo "error code :" .
         * $result->statusCode . "<br>"; echo "error msg :" . $result->statusMsg
         * . "<br>"; //TODO 添加错误处理逻辑 }else{ echo "Sendind TemplateSMS
         * success!<br/>"; // 获取返回信息 $smsmessage = $result->TemplateSMS; echo
         * "dateCreated:".$smsmessage->dateCreated."<br/>"; echo
         * "smsMessageSid:".$smsmessage->smsMessageSid."<br/>"; //TODO 添加成功处理逻辑
         * }
         */
        return $result;
    }
    // Demo调用,参数填入正确后，放开注释可以调用
    // sendTemplateSMS("手机号码","内容数据","模板Id");

    /**
     * 发送模板短信
     *
     * @param $mobile   手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
     * @param $content    短信内容
     * @return mixed|SimpleXMLElement
     */
    public static function sendManDaoTemplateSMS_j4now ($mobile,$content, $ext = '') {
        $sn = Config::getConfig('mandao_sn');
        $pwd = Config::getConfig('mandao_pwd');

        $data = array(
            'sn' => $sn, //提供的账号
            'pwd' => strtoupper(md5($sn . $pwd)), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
            'mobile' => $mobile, //手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
            'content' =>htmlspecialchars($content), //短信内容
            //htmlspecialchars() 函数把一些预定义的字符转换为 HTML 实体。
            'ext' => $ext,
            'stime' => '', //定时时间 格式为2011-6-29 11:09:21
            'rrid' => '',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
            'msgfmt'=>''
        );
        Debug::trace($data);

        $url = "http://sdk.entinfo.cn:8061/webservice.asmx/mdsmssend";

//        $result = FUtil::curlPost($url, $data, 5);
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        $data = http_build_query($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);

        $result=str_replace("<?xml version=\"1.0\" encoding=\"utf-8\"?>","",$result);
        $result=str_replace("<string xmlns=\"http://tempuri.org/\">","",$result);
        $result=str_replace("<string xmlns=\"http://entinfo.cn/\">","",$result);
        $result=str_replace("</string>","",$result);
        return $result;
    }

}
