<?php

class BaseBSP extends BaseSupport
{

    protected $config = [
        'accesscode' => 'BSPdevelop' ,                 //商户号码
        'checkword' => 'j8DzkIFgmlomPt0aLuwU',         //商户密匙
        'server' => "http://bsp-ois.sit.sf-express.com:9080/",
        'server_ssl' => "https://bsp-ois.sit.sf-express.com:9443/",
        'ssl' => false,
        'uri' => 'bsp-ois/sfexpressService',
    ];

    protected $ret = array(
        'head' => "ERR",
        'message' => '系统错误',
        'code' => -1
    );

    public function __construct($params = null)
    {
        if (null != $params) {
            $this->config = array_merge($this->config, $params);
        }
    }

    /**
     * 顺丰BSP接口主程序
     * @param $xml
     * @return bool|mixed
     */
    public function postXmlBodyWithVerify($xml){
        $xml=$this->buildXml($xml);
        $verifyCode=$this->sign($xml, $this->config['checkword']);
        $post_data="xml=$xml&verifyCode=$verifyCode";
        $response=$this->postXmlCurl($post_data,$this->getPostUrl());
        return $response;
    }

    /**
     * get request service name
     * @param null $class
     * @return string
     */
    public function getServiceName($class=null) {
        $name = "";
        if (empty($class)) {
            $name = basename(str_replace('\\', '/', get_called_class()),'.php');
        }else{
            $name = basename(str_replace('\\', '/', $class),'.php');
        }
        return str_replace('BSP', '', $name);
    }

    /**
     * 拼接XML字符串
     * @param $bodyData
     * @return string
     */
    public function buildXml($bodyData){
        $xml = '<Request service="'.$this->getServiceName().'" lang="zh-CN">' .
            '<Head>'.$this->config['accesscode'].'</Head>' .
            '<Body>' . $bodyData . '</Body>' .
            '</Request>';
        return $xml;
    }

    /**
     * 获取POST地址
     * @return string
     */
    protected function getPostUrl(){
        if($this->config['ssl']){
            return $this->config['server_ssl'].$this->config['uri'];
        } else {
            return $this->config['server'].$this->config['uri'];
        }
    }

}
