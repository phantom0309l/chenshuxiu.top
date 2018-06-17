<?php

/**
 * 微信红包helper
 */
class WxRedPackHelper extends WxPayDataBase
{

    public $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";

    // ----0------------------------------------------------------------

    // 设置基本参数
    public function setBaseParams ($openid, $amount, $mch_billno = '') {
        $config = new WxPayConfig();
        $mch_id = $config->getMCHID();
        $wxappid = $config->getAPPID();
        $re_openid = $openid;
        $total_amount = $amount;

        $client_ip = XUtility::getonlineip();
        if (empty($client_ip)) {
            $client_ip = "123.56.89.157";
        }

        // 订单号
        if (empty($mch_billno)) {
            $mch_billno = $mch_id . date('YmdHis') . rand(1000, 9999);
        }

        $this->setValue("nonce_str", $this->great_rand()); // 随机字符串
        $this->setValue("mch_billno", $mch_billno); // 订单号
        $this->setValue("mch_id", $mch_id); // 商户号
        $this->setValue("wxappid", $wxappid); // 公众账号appid
        $this->setValue("re_openid", $re_openid); // 用户openid
        $this->setValue("total_amount", $total_amount); // 付款金额，单位分
        $this->setValue("total_num", 1); // 红包发放总人数
        $this->setValue("client_ip", $client_ip); // 调用接口的机器 Ip 地址
    }

    // 设置参数
    public function setValue ($key, $value) {
        $this->values[$key] = $value;
    }

    private function great_rand () {
        $t1 = "";
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < 30; $i ++) {
            $j = rand(0, 35);
            $t1 .= $str[$j];
        }
        return $t1;
    }

    // 那里用了呢?
    public function check_sign_parameters () {
        if ($this->params["nonce_str"] == null || $this->params["mch_billno"] == null || $this->params["mch_id"] == null || $this->params["wxappid"] == null ||
                 $this->params["nick_name"] == null || $this->params["send_name"] == null || $this->params["re_openid"] == null ||
                 $this->params["total_amount"] == null || $this->params["max_value"] == null || $this->params["total_num"] == null ||
                 $this->params["wishing"] == null || $this->params["client_ip"] == null || $this->params["act_name"] == null || $this->params["remark"] == null ||
                 $this->params["min_value"] == null) {
            return false;
        }
        return true;
    }

    // ----1------------------------------------------------------------

    // 执行发红包操作
    public function dowork () {
        $postXml = $this->create_redpack_xml();
        $url = $this->url;
        $responseXml = $this->curl_post_ssl($url, $postXml);
        return $this->xmlToArray($responseXml);
    }

    // 结果转换成数组
    private function xmlToArray ($xml) {
        return $this->FromXml($xml);
    }

    // 生成红包接口XML信息
    /*
     * <xml> <sign>![CDATA[E1EE61A9]]</sign>
     * <mch_billno>![CDATA[00100]]</mch_billno> <mch_id>![CDATA[888]]</mch_id>
     * <wxappid>![CDATA[wxcbda96de0b165486]]</wxappid>
     * <nick_name>![CDATA[nick_name]]</nick_name>
     * <send_name>![CDATA[send_name]]</send_name>
     * <re_openid>![CDATA[onqOjjXXXXXXXXX]]</re_openid>
     * <total_amount>![CDATA[100]]</total_amount>
     * <min_value>![CDATA[100]]</min_value> <max_value>![CDATA[100]]</max_value>
     * <total_num>![CDATA[1]]</total_num> <wishing>![CDATA[恭喜发财]]</wishing>
     * <client_ip>![CDATA[127.0.0.1]]</client_ip>
     * <act_name>![CDATA[新年红包]]</act_name> <act_id>![CDATA[act_id]]</act_id>
     * <remark>![CDATA[新年红包]]</remark> </xml>
     */
    private function create_redpack_xml () {
        $this->SetSign();
        return $this->ToXml();
    }

    // 发网络请求
    private function curl_post_ssl ($url, $vars, $second = 30, $aHeader = array()) {
        $ch = curl_init();
        // 超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 以下两种方式需选择一种

        // 第一种方法，cert 与 key 分别属于两个.pem文件
        // 默认格式为PEM，可以注释
        $sslCertPath = WxPayConfig::getSSLCERT_PATH();
        $sslKeyPath = WxPayConfig::getSSLKEY_PATH();
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
        // 默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);

        // 第二种方式，两个文件合成一个.pem文件
        // curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
}
