<?php

class BaseSupport
{
    /**
     * 计算验证码
     * data 是拼接完整的报文XML
     * check_word 是顺丰给的接入码
     *
     * @param string $data
     * @param string $check_word
     * @return string
     */
    public static function sign($data, $check_word) {
        $string = trim($data).trim($check_word);
        $md5 = md5(mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string)), true);
        $sign = base64_encode($md5);
        return $sign;
    }

    /**
     * XML to array.
     *
     * @param string $xml XML string
     *
     * @return array|\SimpleXMLElement
     */
    public static function parse($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = self::arrarval($data);
        }
        return $data;
    }

    /**
     * XML to object
     * @param $xml
     * @return \SimpleXMLElement
     */
    public static function parseRaw($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        return $data;
    }

    /**
     * Object to array.
     *
     * @param string $data
     *
     * @return array
     */
    private static function arrarval($data)
    {
        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = (array) $data;
        }
        if (is_array($data)) {
            foreach ($data as $index => $value) {
                $data[$index] = self::arrarval($value);
            }
        }
        return $data;
    }

    /**
     * 转换顺丰返回XML
     * @param $data
     * @param $name
     * @return array
     */
    public function getResponse($data, $name) {
        $ret = array();
        $xml = @simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if ($xml){
            $ret['head'] = (string)$xml->Head;
            if ($xml->Head == 'OK'){
                $ret = array_merge($ret , $this->getData($xml, $name));
            }
            if ($xml->Head == 'ERR'){
                $ret = array_merge($ret , $this->getErrorMessage($xml));
            }
        }
        return $ret;
    }

    public function getErrorMessage($xml) {
        $ret = array();
        $ret['message'] = (string)$xml->ERROR;
        if (isset($xml->ERROR[0])) {
            foreach ($xml->ERROR[0]->attributes() as $key => $val) {
                $ret[$key] = (string)$val;
            }
        }
        return $ret;
    }

    public function getData($xml, $name) {
        $ret = array();
        if (isset($xml->Body->$name)){
            foreach ($xml->Body->$name as $v) {
                foreach ($v->attributes() as $key => $val) {
                    $ret[$key] = (string)$val;
                }
            }
        }
        return $ret;
    }

    /**
     * 转换属性为XML字符串
     * @param array $params
     * @param string $xml_Name
     * @return string
     */
    protected function paramsToString($params = [], $xml_Name = '')
    {
        $string = '';
        $return_string = '';
        if ($xml_Name && is_array($params)) {
            foreach ($params as $key => $value) {
                if ( is_array($value)){
                    $string = $this->paramsToString($value);
                }else{
                    $string .= " $key=\"$value\"";
                }
                $return_string .= "<$xml_Name$string></$xml_Name>";
            }
        } elseif (!$xml_Name && is_array($params)) {
            foreach ($params as $k => $v) {
                $string .= " $k=\"$v\"";
            }
            $return_string = $string;
        }

        return $return_string;
    }

    /**
     * 作用：以post方式提交xml到对应的接口url
     * @param $data
     * @param $url
     * @param int $second
     * @return bool|mixed
     */
    public function postXmlCurl($data,$url,$second=30)
    {
        echo $data;

        try{
            header("Content-type: text/html; charset=utf-8");
            $ch = curl_init();//初始化curl
            curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
            curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $data = curl_exec($ch);//运行curl
            curl_close($ch);
            return $data;
        }catch (\Exception $e) {
            return false;
        }
    }

}
