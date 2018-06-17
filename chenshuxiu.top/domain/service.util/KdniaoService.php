<?php

class KdniaoService
{
    //用于线上和测试接口切换
    protected $IS_ONLINE = true;

    //接口主部
    protected $URI = "";

    //商户id
    protected $EBusinessID = 1308158;

    //AppKey
    protected $AppKey = 'cb220105-303c-4c06-936b-471666cb22a6';

    public function __construct()
    {
        $URI = $this->URI = $this->IS_ONLINE ? "http://api.kdniao.cc/api" : "http://testapi.kdniao.cc:8081";

        //电子运单接口
        $this->ReqURL_eorder = "{$URI}/Eorderservice";

        //获取代收货款订单状态接口
        $this->ReqURL_reg = "{$URI}/reg";

        //即时查询API
        $this->ReqURL_orderTrace = "http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx";

        //物流跟踪API
        $this->ReqURL_orderTraceSub = "{$URI}/dist";



    }

    /**
    * 获取电子面单数据
    */

    /*
    失败：
      {
          "EBusinessID": "1237100",
          "ResultCode": "105",
          "Reason": "订单号已存在，请勿重复操作"，
          "UniquerRequestNumber":"5e66486b-8fbc-4131-b875-9b13d2ad1354"
      }
      成功：
      {
        "EBusinessID": "1237100",
        "Order": {
          "OrderCode": "012657700387",
          "ShipperCode": "HTKY",
          "LogisticCode": "50002498503427",
          "MarkDestination": "京-朝阳(京-1)",
          "OriginCode": "200000",
          "OriginName": "上海分拨中心",
          "PackageCode": "北京"
        },
        "PrintTemplate":"此处省略打印模板HTML内容",
        "EstimatedDeliveryTime":"2016-03-06",
        "Callback":"调用时传入的Callback",
        "Success": true,
        "ResultCode": "100",
        "Reason": "成功"
    }*/

    //参考文档：http://www.kdniao.com/api-eorder
    function getEOrderData($arr){
        $jsonParam = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $jsonResult = $this->submitEOrder($jsonParam);
        $result = json_decode($jsonResult, true);
        return $result;
    }

    /**
     * Json方式 调用电子面单接口
     */
    function submitEOrder($requestData){
        $EBusinessID = $this->EBusinessID;
        $AppKey = $this->AppKey;
        $ReqURL = $this->ReqURL_eorder;

    	$datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => '1007',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
    	$result=$this->sendPost($ReqURL, $datas);

    	//根据公司业务处理返回的信息......

    	return $result;
    }

    //=============================[收货款状态查询接口]====================================================
    //参考文档：http://www.kdniao.com/api-cod 页面稍微往下的地方，第三个标题=>3订单类
    public function getRegData($arr){
        $jsonParam = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $jsonResult = $this->submitReg($jsonParam);
        $result = json_decode($jsonResult, true);
        return $result;
    }

    /**
     * 调用代收货款状态查询接口
     */
    private function submitReg($requestData){
        $EBusinessID = $this->EBusinessID;
        $AppKey = $this->AppKey;
        $ReqURL = $this->ReqURL_reg;

    	$datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => 'CMD1010',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
    	$result=$this->sendPost($ReqURL, $datas);

    	//根据公司业务处理返回的信息......

    	return $result;
    }

    //=============================[即时查询API]====================================================
    /**
     * Json方式 查询订单物流轨迹 网页上叫，即时查询API
     */
    public function getOrderTraces($arr){
        $requestData = json_encode($arr, JSON_UNESCAPED_UNICODE);
        //$requestData= "{'OrderCode':'','ShipperCode':'SF','LogisticCode':'617709572650'}";
        $EBusinessID = $this->EBusinessID;
        $AppKey = $this->AppKey;
        $ReqURL = $this->ReqURL_orderTrace;

    	$datas = array(
            'EBusinessID' => $EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
    	$result=$this->sendPost($ReqURL, $datas);
        $result = json_decode($result, true);
        Debug::trace("快递鸟查询物流信息：", $result);

    	return $result;
    }

    //=============================[物流信息订阅]====================================================
    //TODO by xiaoqiao，只有小乔的一个测试类在调用
    //$requestData="{'OrderCode': '','ShipperCode':'SF','LogisticCode':'617709572650'}";
//    public function orderTracesSub($arr){
//        $requestData = json_encode($arr, JSON_UNESCAPED_UNICODE);
//        $EBusinessID = $this->EBusinessID;
//        $AppKey = $this->AppKey;
//        $ReqURL = $this->ReqURL_orderTraceSub;
//
//    	$datas = array(
//            'EBusinessID' => $EBusinessID,
//            'RequestType' => '1008',
//            'RequestData' => urlencode($requestData) ,
//            'DataType' => '2',
//        );
//        $datas['DataSign'] = $this->encrypt($requestData, $AppKey);
//    	$result=$this->sendPost($ReqURL, $datas);
//        $result = json_decode($result, true);
//
//    	return $result;
//    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
    	if(empty($url_info['port']))
    	{
    		$url_info['port']=80;
    	}
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
    	$headerFlag = true;
    	while (!feof($fd)) {
    		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
    			break;
    		}
    	}
        while (!feof($fd)) {
    		$gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
    /**************************************************************
     *
     *  使用特定function对数组中所有元素做处理
     *  @param  string  &$array     要处理的字符串
     *  @param  string  $function   要执行的函数
     *  @return boolean $apply_to_keys_also     是否也应用到key上
     *  @access public
     *
     *************************************************************/
    function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**************************************************************
     *
     *  将数组转换为JSON字符串（兼容中文）
     *  @param  array   $array      要转换的数组
     *  @return string      转换得到的json字符串
     *  @access public
     *
     *************************************************************/
    function JSON($array) {
        arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

}
