<?php
require_once ("../../../core/tools/AESCrypt.php");
require_once ("../../../core/tools/RSACrypt.php");


class OcrService {

    const url = 'http://tmai.qq.com';    // 服务器地址
    const appid = 'yr5j9hyu7cyurhe9ickzfyqbtbo259zpvs054s43mxavwau8skp97rnotn1g2d9y';    // appid
    const appkey = 'niwkmqslubxk6hja';    // appkey
    const cmdEncrypt = 'fjehwyp9tswxclrw4vtpe1l4o5zsgis28k6bldk168vgz5z1bvxtki9j2ijjraeq';   // 加密cmd
    const cmdUnEncrypt = '97wxaa25a6yv53cys5ny9r786qromoja7e65hex1r48rk733i8pls4tic4pa67sn';    // 非加密cmd

    const rsa_public_key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCOVapB86KyLRhFUZM6lYeImtDJLydnT3rZJ+iJbyxE3Lniz946lXlskZ8+pjslECoBuj/EKcMnKBZ97R5pmwtQSgi8HfxehHuDJW2f9cT97KunbNXRVJtwV6GWXQ+pCIGoZCZyweE3eB4ecsZBa5l9cJglk9f6GjCrEbGXC+tY5QIDAQAB";

    // 发送post请求给ocr，并返回响应体
    public static function sendPostRequest ($headers,$body) {
        $data = FUtil::curlPost(self::url,$body,10,$headers);
        list($headerResult, $bodyResult) = explode("\r\n\r\n", $data, 2);
        return $bodyResult;
    }

    // 获取Http请求头
    public static function getDefautlHttpHeader (String $body,$isEncrypt=false) {
        $headers = array();
        $headers[] = "appid:".self::appid."";
        $headers[] = $isEncrypt?"cmd:".self::cmdEncrypt."":"cmd:".self::cmdUnEncrypt."";

        if($isEncrypt) {
            $sign = self::getsign($body , self::rsa_public_key);
            $headers[] = "sign:{$sign}";
        }
        return $headers;
    }

    // 获取post请求体
    public static function getDefaultPostFields (Array $arr,$isEncrypt=false) {
        $str = json_encode($arr);   // 原始字符串

        // 设置body 请求体
        if($isEncrypt){
            $aes = new AESCrypt(self::appkey);
            $body = $aes->encrypt($str);
        }else {
            $body = $str;
        }

        return $body;
    }

    public static function getsign (String $body,$rsa_public_key) {
        $a =  base64_encode(md5($body));
        $str = chunk_split($rsa_public_key, 64, "\n");
        $public_key = "-----BEGIN PUBLIC KEY-----\n$str-----END PUBLIC KEY-----\n";
        $rsa = new RSACrypt($public_key);
        $b = $rsa->encrypt($a);
        return $b;
    }

    // 格式化数据
    public static function formdateJson (Array $data,$type=1) {
        $defaultJson = self::getDefaultJson($type);
        foreach($data as $key=>$item){
            if('patientInfo' == $key){
                $data['patientInfo']['date'] = $data['date'];
                $data['patientInfo'] = array_merge($defaultJson['patientInfo'],$data['patientInfo']);;
            }else if(is_array($item)){
                foreach($item as $k=>$i){
                    if(is_array($i)){
                        $data[$key][$k] = array_merge($defaultJson[$key],$i);
                    }
                }
            }
        }
        if($type == 1 && empty($data['items'])){
            $data['items'] = $defaultJson['items'];
            $data['status'] = 0;
        }
        if($type == 3 && empty($data['drugList'])){
            $data['drugList'] = $defaultJson['drugList'];
            $data['status'] = 0;
        }
        return $data;
    }

    // 获取默认的Json数据
    public static function getDefaultJson ($type=1){
        if($type == 1){
            // 检查报告
            $arr = array(
                'errorcode'   => 0,
                'errormsg'    => '',
                'text'        => '',
                'patientInfo' => array(
                    'name'    =>  '',
                    'sex'     =>  '',
                    'age'     =>  '',
                    'date'    => ''
                ),
                'items'       =>  array(
                    'code'    =>  '',
                    'name'    =>  '',
                    'result'  =>  '',
                    'unit'    =>  '',
                    'range'   =>  ''
                )
            );
        }else if($type == 2){
            $arr = array(
                'errorcode'   => 0,
                'errormsg'    => '',
                'text'        => '',
                'drugName'    => '',
                'drugFactory' => ''
            );
        }else if($type == 3){
            $arr = array(
                'errorcode'   => 0,
                'errormsg'    => '',
                'text'        => '',
                'patientInfo' => array(
                    'name'    =>  '',
                    'sex'     =>  '',
                    'age'     =>  ''
                ),
                'date'        => '',
                'drugList'    => array(
                    'name'    => '',
                    'weight'  => ''
                )
            );
        }else {
            $arr = array(
                'errorcode'   => 0,
                'errormsg'    => '',
            );
        }
        return $arr;
    }

    // 根据类型获取表头数组
    public static function getTableHeader ($type=1){
        if($type == 1){
            return array('name','sex','age','date');              // patientInfo
        }elseif($type == 2){
            return array( 'code','name','result','unit','range'); // 检查报告items
        }elseif($type == 3){
            return array('drugName','drugFactory');               // 药盒
        }elseif($type == 4){
            return array('name','weight');                        // 处方单
        }else {
            return array('errorcode','errormsg');                 // ocr错误
        }
    }
}