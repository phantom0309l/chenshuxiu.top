<?php
// 创建: 2018-01-12 by txj
// 备注: 快递服务
class ExpressService
{
    const KDNIAO = "kdniao";

    const KD100 = "kd100";
    //-------------------[获取快递踪迹][begin]--------------------------------------------------
    public static function getTraces(ShopPkg $shopPkg) {
        $result = null;

        $isSF = $shopPkg->isSF();
        //顺丰的订单查询走kdniao
        if($isSF){
            $result = self::getTraces_kdniao($shopPkg);
        }else{
            $result = self::getTraces_kd100($shopPkg);
        }
        return $result;
    }

    private static function getTraces_kdniao(ShopPkg $shopPkg) {
        $express_no = $shopPkg->express_no;
        if ($express_no) {

            //先尝试从express_trace表获取数据
            $result = self::getTraces_express_trace($shopPkg);
            if($result != null){
                Debug::trace("express_trace表中查询到了快递信息【shoppkgid:{$shopPkg->id}】");
                return $result;
            }

            //获取不到再尝试即时查询
            $enames = CtrHelper::getExpress_companyOfEnameCtrArray(self::KDNIAO);
            $type = $enames[$shopPkg->express_company];
            if (empty($type)) {
                return null;
            }

            $requestData = [];
            $requestData["OrderCode"] = "";
            $requestData["ShipperCode"] = $type;
            $requestData["LogisticCode"] = $express_no;

            $kdniaoservice = new KdniaoService();
            $result = $kdniaoservice->getOrderTraces($requestData);
            $result = self::reworkResultData($result, self::KDNIAO);
            return $result;
        } else {
            Debug::trace("没有快递号【shoppkgid:{$shopPkg->id}】");
            return null;
        }
    }

    private static function getTraces_kd100(ShopPkg $shopPkg) {
        $express_no = $shopPkg->express_no;
        if ($express_no) {
            $enames = CtrHelper::getExpress_companyOfEnameCtrArray(self::KD100);
            $type = $enames[$shopPkg->express_company];
            if (empty($type)) {
                return null;
            }

            //"https://m.kuaidi100.com/index_all.html?type=shunfeng&postid=824708891316";
            $url = "https://m.kuaidi100.com/query?type={$type}&postid={$express_no}";
            $str = file_get_contents($url);
            $result = json_decode(trim($str), true);
            $result = self::reworkResultData($result, self::KD100);
            Debug::trace("快递100查询物流信息：", $result);
            return $result;
        } else {
            return null;
        }
    }

    //从express_trace表获取追踪数据
    private static function getTraces_express_trace(ShopPkg $shopPkg){
        $express_no = $shopPkg->express_no;
        $express_trace = Express_traceDao::getOneByExpress_no($express_no);
        if($express_trace instanceof Express_trace){
            $traces = $express_trace->traces;
            if($traces){
                $traces = json_decode($traces, true);
                $data = array(
                    "Success" => true,
                    "State" => $express_trace->state,
                    "Traces" => $traces
                );
                $result = self::reworkResultData($data, self::KDNIAO);
                return $result;
            }
        }
        return null;
    }

    private static function reworkResultData($result, $data_from){
        $data = array();
        //接口数据是否正确返回, false:错误; true:正确
        $data["Success"] = false;
        //是否在途中,0:否; 1:是
        $data["IsOnTheWay"] = 0;
        //快递路径数组 TraceItem => {"AcceptStation":"快件到达 【北京紫竹院集散中心】","AcceptTime":"2018-01-09 20:10:17"}
        $data["Traces"] = array();

        if($data_from == self::KDNIAO){
            $success = $result["Success"];
            if($success != true){
                return $data;
            }
            $data["Success"] = true;

            //物流状态：2-在途中,3-签收,4-问题件
            $state = $result["State"];
            if($state == 2){
                $data["IsOnTheWay"] = 1;
            }
            $traces = array_reverse($result["Traces"]);
            $data["Traces"] = $traces;

            return $data;
        }

        if($data_from == self::KD100){
            //status值为200时，查询成功
            $status = $result["status"];
            if($status != 200){
                return $data;
            }
            $data["Success"] = true;

            //0：在途中,1：已发货，2：疑难件，3： 已签收 ，4：已退货。
            $state = $result["state"];
            if($state == 0 || $state == 1){
                $data["IsOnTheWay"] = 1;
            }

            $traces = array();
            $temp_arr = $result["data"];
            foreach($temp_arr as $temp){
                $one = array();
                $one["AcceptTime"] = $temp["time"];
                $one["AcceptStation"] = $temp["context"];
                $traces[] = $one;
            }
            $data["Traces"] = $traces;

            return $data;
        }

        return $result;
    }
    //-------------------[获取快递踪迹][end]--------------------------------------------------

    //-------------------[发送订单号][begin]--------------------------------------------------
    public static function sendExpress_no(ShopPkg $shopPkg){
        $wxuser = $shopPkg->wxuser;
        if ($wxuser instanceof WxUser) {
            $patient = $shopPkg->patient;
            $diseaseid = $patient->diseaseid;
            $isCancer = Disease::isCancer($diseaseid);
            $isADHD = Disease::isADHD($diseaseid);

            $express_company = $shopPkg->express_company;
            $express_no = $shopPkg->express_no;

            $product_name = $shopPkg->shoporder->isChufang() ? "药品" : "商品";
            $fix_str = $isADHD ? "家长" : "";


            $wx_uri = Config::getConfig("wx_uri");
            $url = "{$wx_uri}/shopmedicine/showExpressStep?express_no={$express_no}&shoppkgid={$shopPkg->id}";
            $end_fix_str = "";
            if($isCancer){
                $end_fix_str = "揽收后您可点击<a href=\"{$url}\">『此处查看』</a>快递进展。";
            }else{
                $end_fix_str = "揽收后您可点击『开药门诊』菜单，或点击<a href=\"{$url}\">『此处查看』</a>快递进展。";
            }
            $content = "{$patient->name}{$fix_str}您好！您的{$product_name}已打包完毕，预计将由({$express_company}快递)揽收，运单号是：{$express_no}。{$end_fix_str}";
            XContext::setValue('is_filter_doubtlist', false);
            PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
        }
    }
    //-------------------[发送订单号][end]--------------------------------------------------


    //-------------------[快递追踪订阅][begin]--------------------------------------------------
    //TODO by xiaoqiao，只有小乔的一个测试类在调用
//    public static function tracesSub(ShopOrder $shopOrder){
//        $enames = CtrHelper::getExpress_companyOfEnameCtrArray(self::KDNIAO);
//        $express_company = $shopOrder->express_company;
//        $type = $enames[$express_company];
//        if (empty($type)) {
//            Debug::warn("find ShipperCode fail by express_company[{$express_company}]");
//            return;
//        }
//
//        $express_no = $shopOrder->express_no;
//        $express_trace = Express_traceDao::getOneByExpress_no($express_no);
//        if($express_trace instanceof Express_trace && $express_trace->isSub()){
//            return;
//        }
//
//        $requestData = [];
//        $requestData["OrderCode"] = "";
//        $requestData["ShipperCode"] = $type;
//        $requestData["LogisticCode"] = $express_no;
//
//        //result[{"UpdateTime":"2018-01-20 15:08:51","EBusinessID":"1308158","Success":true}]
//        $kdniaoservice = new KdniaoService();
//        $result = $kdniaoservice->orderTracesSub($requestData);
//
//
//        if(false == $express_trace instanceof Express_trace){
//            //创建express_trace
//            $row = [];
//            $row["shoporderid"] = $shopOrder->id;
//            $row["express_no"] = $express_no;
//            $row["sub_time"] = $result["UpdateTime"];
//
//            $success = $result["Success"];
//            if($success == true){
//                $row["sub_status"] = 1;
//            }else{
//                $reason = isset($result["Reason"]) ? $result["Reason"] : "";
//                $row["sub_reason"] = $reason;
//            }
//
//            Express_trace::createByBiz($row);
//        }else{
//            $success = $result["Success"];
//            if($success == true){
//                $express_trace->sub_status = 1;
//            }else{
//                $reason = isset($result["Reason"]) ? $result["Reason"] : "";
//                $express_trace->sub_reason = $reason;
//            }
//        }
//
//        return $result;
//    }
    //-------------------[快递追踪订阅][end]--------------------------------------------------

}
