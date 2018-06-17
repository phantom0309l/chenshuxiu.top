<?php

class XHttpRequest
{

    private $curl;

    private $success;

    private static $instance = null;

    private function __construct () {
        $this->curl = new Net_Curl();
        $this->curl->header = false;
        $this->curl->timeout = 60; // TODO 60 秒
        $this->curl->userAgent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Maxthon)";
        $result = $this->curl->create();
        if (PEAR::isError($result)) {
            echo "init curl error " . $result->getMessage() . "\n";
            $this->success = false;
        } else
            $this->success = true;
    }

    public function __destruct () {
        if ($this->success)
            $this->curl->close();
    }

    public static function getInstance () {
        if (self::$instance == null)
            self::$instance = new XHttpRequest();

        return self::$instance;
    }

    public function getUrlContents ($url, &$err) {
        return $this->requestUrlContents($url, 'GET', null, $err);
    }

    public function postUrlContents ($url, $fields, &$err) {
        return $this->requestUrlContents($url, 'POST', $fields, $err);
    }

    private function requestUrlContents ($url, $type, $fields, &$err) {
        $err = '';
        if (! is_string($url) || $url == '') {
            $err = 'not init url';
            return false;
        }

        $this->curl->url = $url;
        $this->curl->type = $type;
        $this->curl->fields = $fields;

        $result = $this->curl->execute();
        if (! PEAR::isError($result))
            return $result;
        for ($i = 0; $i < 2; $i ++) {
            $result = $this->curl->execute();
            if (! PEAR::isError($result))
                return $result;

            $err = $result->getMessage();
            sleep(1);
        }

        return false;
    }

    public function setCookies ($cookies) {
        $this->curl->cookies = $cookies;
    }

    public function setHeader ($header) {
        $this->curl->header = $header;
    }

    public function setHttpHeaders ($httpHeaders) {
        $this->curl->httpHeaders = $httpHeaders;
    }

    public function setUserAgent ($userAgent) {
        $this->curl->userAgent = $userAgent;
    }

    public function setProxy ($proxy, $proxyUser = null, $proxyPassword = null) {
        if ($proxy) {
            $this->curl->proxy = $proxy;
            $this->curl->proxyUser = $proxyUser;
            $this->curl->proxyPassword = $proxyPassword;
        }
    }

    public function getCurl () {
        return $this->curl;
    }

    // /////////////////////////////////////////////////////////////////////////////////
    //
    public static function my_file_get_contents ($url, $timeout = 10) {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => $timeout)));
        return $data = @file_get_contents($url, 0, $ctx);
    }

    public static function curl_getUrlContents ($url, &$err = null) {
        $httpRequest = XHttpRequest::getInstance();
        return $httpRequest->getUrlContents($url, $err);
    }

    public static function curl_postUrlContents ($url, $fields, &$err = null) {
        $httpRequest = XHttpRequest::getInstance();
        $httpRequest->setHttpHeaders(array(
            "Content-type: text/html"));
        return $httpRequest->postUrlContents($url, $fields, $err);
    }

    /*
     * $xmlData = " <xml><ToUserName><![CDATA[ad775b217]]></ToUserName>
     * <FromUserName><![CDATA[tWy3zC3xUgQMR5coXif5SA]]></FromUserName>
     * <CreateTime>1366181013</CreateTime> <MsgType><![CDATA[text]]></MsgType>
     * <Content><![CDATA[我的测试]]></Content> <MsgId>5867702771251151243</MsgId>
     * </xml>";
     */
    public static function curl_postXmlData ($url, $xmlData, &$err) {
        // $url = 'http://wang.net/xml/getXml.php'; //接收xml数据的文件
        $header[] = "Content-type: text/xml"; // 定义content-type为xml,注意是数组
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $err = curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }
}
