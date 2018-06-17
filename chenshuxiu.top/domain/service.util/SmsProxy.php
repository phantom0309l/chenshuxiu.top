<?php

class SmsProxy
{

    static $_smsSendByYimeiTrigger = 0;

    static $_smsSendByManDaoTrigger = 1;

    public function send ($phone_list, $content, $timeout = 0, $response_timeout = 30) {
        $result = array();
        if (self::$_smsSendByYimeiTrigger) {
            $result = self::sendByYimei($phone_list, $content, $timeout = 0, $response_timeout = 30);
        } elseif (self::$_smsSendByManDaoTrigger) {
            $result = self::sendByMandao($phone_list, $content);
        } else {
            // do nothing
        }

        $row = array();
        $row['mobile'] = $phone_list;
        $row['content'] = $content;
        $row['errcode'] = isset($result['errcode']) ? $result['errcode'] : - 1;
        $row['errmsg'] = isset($result['errmsg']) ? $result['errmsg'] : "";

        Sms::createByBiz($row);

        return $result;
    }

    public function getBalance () {

        /**
         * 网关地址
         */
        $gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';

        /**
         * 序列号,请通过亿美销售人员获取
         */
        $serialNumber = 'abc';

        /**
         * 密码,请通过亿美销售人员获取
         */
        $password = 'xyz';

        /**
         * 登录后所持有的SESSION KEY，即可通过login方法时创建
         */
        $sessionKey = 'HcdjpvFQPL';

        /**
         * 连接超时时间，单位为秒
         */
        $connectTimeOut = 0;

        /**
         * 远程信息读取超时时间，单位为秒
         */
        $readTimeOut = 30;

        /**
         * $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
         * $proxyport		可选，代理服务器端口，默认为 false
         * $proxyusername	可选，代理服务器用户名，默认为 false
         * $proxypassword	可选，代理服务器密码，默认为 false
         */
        $proxyhost = false;
        $proxyport = false;
        $proxyusername = false;
        $proxypassword = false;

        $client = new EmaySmsClient($gwUrl, $serialNumber, $password, $sessionKey, $proxyhost, $proxyport, $proxyusername, $proxypassword, $connectTimeOut,
                $readTimeOut);
        return $client->getBalance();
    }

    public function getMO () {

        /**
         * 网关地址
         */
        $gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';

        /**
         * 序列号,请通过亿美销售人员获取
         */
        $serialNumber = 'abc';

        /**
         * 密码,请通过亿美销售人员获取
         */
        $password = 'xyz';

        /**
         * 登录后所持有的SESSION KEY，即可通过login方法时创建
         */
        $sessionKey = 'HcdjpvFQPL';

        /**
         * 连接超时时间，单位为秒
         */
        $connectTimeOut = 0;

        /**
         * 远程信息读取超时时间，单位为秒
         */
        $readTimeOut = 30;

        /**
         * $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
         * $proxyport		可选，代理服务器端口，默认为 false
         * $proxyusername	可选，代理服务器用户名，默认为 false
         * $proxypassword	可选，代理服务器密码，默认为 false
         */
        $proxyhost = false;
        $proxyport = false;
        $proxyusername = false;
        $proxypassword = false;

        $client = new EmaySmsClient($gwUrl, $serialNumber, $password, $sessionKey, $proxyhost, $proxyport, $proxyusername, $proxypassword, $connectTimeOut,
                $readTimeOut);
        return $moResult = $client->getMO();
    }

    public function sendByYimei ($phone_list, $content, $timeout = 0, $response_timeout = 30) {
        if (! is_array($phone_list)) {
            $phone_list = trim($phone_list, ", ");
            $phone_list = explode(",", $phone_list);
        }

        $phone_list = array_slice($phone_list, 0, 100);

        /**
         * 网关地址
         */
        $gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';

        /**
         * 序列号,请通过亿美销售人员获取
         */
        $serialNumber = 'abc';

        /**
         * 密码,请通过亿美销售人员获取
         */
        $password = 'xyz';

        /**
         * 登录后所持有的SESSION KEY，即可通过login方法时创建
         */
        $sessionKey = 'HcdjpvFQPL';

        /**
         * 连接超时时间，单位为秒
         */
        $connectTimeOut = $timeout;

        /**
         * 远程信息读取超时时间，单位为秒
         */
        $readTimeOut = $response_timeout;

        /**
         * $proxyhost		可选，代理服务器地址，默认为 false ,则不使用代理服务器
         * $proxyport		可选，代理服务器端口，默认为 false
         * $proxyusername	可选，代理服务器用户名，默认为 false
         * $proxypassword	可选，代理服务器密码，默认为 false
         */
        $proxyhost = false;
        $proxyport = false;
        $proxyusername = false;
        $proxypassword = false;

        $client = new EmaySmsClient($gwUrl, $serialNumber, $password, $sessionKey, $proxyhost, $proxyport, $proxyusername, $proxypassword, $connectTimeOut,
                $readTimeOut);

        /**
         * 发送向服务端的编码
         */
        $client->setOutgoingEncoding("UTF-8");

        /**
         * 发送短信
         */
        $ret = false;
        $logMsg = "mobile: " . implode(",", $phone_list) . "; content: " . $content . "; ";

        $content .= "【方寸医生】";
        $statusCode = $client->sendSMS($phone_list, $content);
        $logMsg .= "status: " . $statusCode . "; ";
        if ($statusCode != null && $statusCode == "0") {
            // 成功
            $ret = true;
            $logMsg .= "ok;";
        } else {
            // 失败
            $ret = false;
            $logMsg .= "error: " . $client->getError() . ";";
        }

        $log = new DefaultLogger(EnvSetting::$tmpDir . 'smssoap/');
        $log->smsSoap($logMsg);

        return $ret;
    }

    public function sendByMandao ($phone_list, $content, $timeout = 10) { /*
                                                                          * {{{
                                                                          */
        if (! (isset($phone_list) && ! empty($phone_list) && ! is_null($phone_list)) ||
                 ! (isset($phone_list) && ! empty($phone_list) && ! is_null($phone_list)))
                    return false;

                $sn = 'SDK-WSS-010-06682';
                $pwd = '9e4-345C';
                $reqUrl = "sdk.entinfo.cn";
                if (! is_array($phone_list)) {
                    $phone_list = trim($phone_list, ", ");
                    $phone_list = explode(",", $phone_list);
                }

                $content .= "【方寸医生】";
                $content = urlencode($content);
                $phone_list = array_slice($phone_list, 0, 100);
                $phone_list = implode(',', $phone_list);

                $postData = array(
                    'sn' => $sn,
                    'pwd' => strtoupper(md5($sn . $pwd)),
                    'mobile' => $phone_list,
                    'content' => $content,
                    'ext' => '',
                    'rrid' => '',
                    'stime' => '');
                $result = array();
                $result['errmsg'] = "mobile: " . $phone_list . "; content: " . urldecode($content) . "; ";
                $result['errmsg'] .= 'mandaosend:';

                $flag = 0;
                $params = '';
                foreach ($postData as $key => $value) {
                    if ($flag != 0) {
                        $params .= "&";
                        $flag = 1;
                    }
                    $params .= $key . "=";
                    $params .= urlencode($value);
                    $flag = 1;
                }
                $length = strlen($params);
                // 创建socket连接
                try {
                    $fp = fsockopen($reqUrl, 8060, $errno, $errstr, $timeout);
                    if (! $fp) {
                        $result['errcode'] = 0;
                        $result['errmsg'] .= "connect host error" . $reqUrl;
                    } else {
                        // 构造post请求的头
                        $header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n";
                        $header .= "Host:{$reqUrl}\r\n";
                        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $header .= "Content-Length: " . $length . "\r\n";
                        $header .= "Connection: Close\r\n\r\n";
                        // 添加post的字符串
                        $header .= $params . "\r\n";
                        // 发送post的数据
                        fputs($fp, $header);
                        $inheader = 1;
                        while (! feof($fp)) {
                            $line = fgets($fp, 1024); // 去除请求包的头只显示页面的返回数据
                            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                                $inheader = 0;
                            }
                            if ($inheader == 0) {

                            }
                        }
                        // <string
                        // xmlns="http://tempuri.org/">261215372218196674</string>
                        preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/is', $line, $str);
                        $res = explode("-", $str[1]);
                        if (count($res) > 1) {
                            $result['errcode'] = 0;
                            $result['errmsg'] .= '发送失败返回值为:' . $line . '。请查看webservice返回值对照表';
                        } else {
                            $result['errcode'] = 1;
                            $result['errmsg'] .= 'ok' . $line;
                        }
                    }
                } catch (Exception $e) {
                    $result['errcode'] = 0;
                    $result['errmsg'] .= "httperror:" . $e->getMessage();
                }
                return $result;
            }

            /*
             * }}}
             */
        }

        if (php_sapi_name() == 'cli' && realpath($_SERVER['SCRIPT_FILENAME']) == __FILE__) {
            $runTime = new RunTime();
            $runTime->start();

            NewSmsVisitor::sendByYimei('15001169262', 'test');

            $runTime->stop();
            print "\ntime spent:" . $runTime->spent() . "s\n";
            print 'Done';
        }
