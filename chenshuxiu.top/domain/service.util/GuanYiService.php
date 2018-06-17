<?php

class GuanYiService
{

    //appkey
    public static $appkey = "185836";

    //sessionkey
    public static $sessionkey = "c3d0f41aa6e34f9cb40e38ef25d8adad";

    //secret
    public static $secret = "54adbca42f604c36950ef171c83c5343";

    //requrl
    public static $requrl = "http://v2.api.guanyierp.com/rest/erp_open";

    //乐库仓库码
    private static $warehouse_code = "BJLK";

    //店铺code
    private static $shop_code = "FCYS";


    //配送单新增基于ShopPkg
    public static function tradeAddByShopPkg (ShopPkg $shopPkg) {
        $data = array();

        //平台编号
        $data["platform_code"] = $shopPkg->fangcun_platform_no;

        //退款
        $data['refund'] = 0;

        //货到付款
        $data['cod'] = false;

        //店铺code
        $data["shop_code"] = self::$shop_code;


        $express_company_arr = CtrHelper::getExpress_companyOfEnameCtrArrayForQiMen();
        $express_company = $shopPkg->express_company;
        $express_code = $express_company_arr[$express_company];

        //物流公司code
        $data["express_code"] = empty($express_code) ? "SFGR" : $express_code;
        //$data["express_code"] = "SF-GJ";

        //仓库code, 写乐库仓库码
        $data["warehouse_code"] = self::$warehouse_code;

        //会员code, 写患者信息？
        $data["vip_code"] = $shopPkg->patientid;

        //物流费用
        $data["post_fee"] = $shopPkg->getExpress_price_yuan();


        $shopAddress = $shopPkg->shoporder->shopaddress;
        //收货人
        $data["receiver_name"] = $shopAddress->linkman_name;

        //收货地址
        $data["receiver_address"] = $shopAddress->getDetailAddress();

        //收货人手机
        $data["receiver_mobile"] = $shopAddress->linkman_mobile;

        //收货人省份
        $xprovince_name = $shopAddress->xprovince->name;
        $data["receiver_province"] = self::getFixProvince($xprovince_name);

        //收货人城市
        $data["receiver_city"] = $shopAddress->xcity->name;

        //收货人区域
        $data["receiver_district"] = $shopAddress->xcounty->name;

        //拍单时间
        $data["deal_datetime"] = $shopPkg->shoporder->time_pay;

        //付款时间
        $data["pay_datetime"] = $shopPkg->shoporder->time_pay;

        //买家留言
        $data["buyer_memo"] = $shopPkg->shoporder->remark;

        //卖家备注
        $data["seller_memo"] = "";

        //发票信息数组
        $invoices = [];

        $temp = [];
        //发票类型  1-普通发票；2-增值发票
        $temp["invoice_type"] = 1;
        //发票抬头
        $temp["invoice_title"] = "";
        //发票内容
        $temp["invoice_content"] = "";
        //发票金额
        $temp["invoice_amount"] = 0.00;
        $invoices[] = $temp;

        $data["invoices"] = $invoices;

        //商品信息数组
        $details = [];

        foreach ($shopPkg->getShopPkgItems() as $shopPkgItem) {
            $shopProduct = $shopPkgItem->shopproduct;
            $temp = [];
            //商品代码
            $temp["item_code"] = $shopProduct->id;
            //规格代码
            $temp["sku_code"] = $shopProduct->sku_code;
            //实际单价
            $temp["price"] = $shopPkgItem->getPrice_yuan();
            //商品数量
            $temp["qty"] = $shopPkgItem->cnt;
            $details[] = $temp;
        }

        $data["details"] = $details;

        //支付信息数组
        $payments = [];
        $temp = [];
        //支付类型code
        $temp["pay_type_code"] = "weixin";
        //支付时间 Timestamp类型 时间戳具体到毫秒
        $paytime = strtotime($shopPkg->shoporder->time_pay) * 1000;
        $temp["paytime"] = $paytime;
        //支付金额
        $temp["payment"] = $shopPkg->getAmount_yuan();
        //支付交易号
        $temp["pay_code"] = $shopPkg->fangcun_platform_no;
        //支付账户
        $temp["account"] = "";
        $payments[] = $temp;

        $data["payments"] = $payments;

        //$descstr = json_encode($data, JSON_UNESCAPED_UNICODE);
        //Debug::trace("===[{$descstr}]====");

        return self::tradeAddImp($data);
    }



    //订单新增
    //gy.erp.trade.add
    public static function tradeAddImp ($data) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.trade.add',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }

    //商品新增基于ShopProduct
    public static function itemAddByShopProduct (ShopProduct $shopProduct) {
        $data = array();

        //商品代码
        $data["code"] = $shopProduct->id;
        //商品名称
        $data["name"] = $shopProduct->title;
        //商品简称
        $data["simple_name"] = "";
        //商品类别code
        $data["category_code"] = $shopProduct->shopproducttype->name;

        $skus = [];
        $temp = [];
        //规格代码
        $temp["sku_code"] = $shopProduct->sku_code;
        //规格名称
        $temp["sku_name"] = $shopProduct->title;
        $skus[] = $temp;
        $data["skus"] = $skus;

        return self::itemAddImp($data);
    }

    //商品新增
    //gy.erp.item.add
    public static function itemAddImp ($data) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.item.add',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }

    //基于shoppkg进行发货单的查询，且是已发货的
    public static function tradeDeliverysGetOfDoneByShopPkg(ShopPkg $shopPkg){
        $data = array();

        //页码
        $data["page_no"] = 1;

        //每页大小
        $data["page_size"] = 10;

        //平台单号
        $data["outer_code"] = $shopPkg->fangcun_platform_no;

        //发货状态
        $data["delivery"] = 1;
        return self::tradeDeliverysGetImp($data);
    }

    //配送单查询基于ShopPkg
    public static function tradeGetByShopPkg (ShopPkg $shopPkg) {
        $data = array();

        //平台编号
        $data["platform_code"] = $shopPkg->fangcun_platform_no;

        return self::tradeGetImp($data);
    }

    //订单查询
    //gy.erp.trade.get
    private static function tradeGetImp ($data) {
        $arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.trade.get',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

        return self::sendPost(self::$requrl, $arr);
    }

    //发货单查询
    //gy.erp.trade.deliverys.get
    public static function tradeDeliverysGetImp ($data) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.trade.deliverys.get',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }

    //采购入库单新增
    public static function purchaseArriveAdd ($sku_code, $qty, $end_date) {
        $data = array();

        //仓库代码
        $data["warehouse_code"] = self::$warehouse_code;

        //供应商代码
        $data["supplier_code"] = "FCYS";

        //商品列表
        $detail_list = [];
        $temp = [];
        $shopProduct = ShopProductDao::getShopProductBySku_code($sku_code);
        if($shopProduct instanceof ShopProduct){
            //商品代码
            $temp["barcode"] = $shopProduct->sku_code;
            //数量
            $temp["qty"] = $qty;
            //有效期 int
            if(!empty($end_date)){
                $temp["shelfLife"] = XDateTime::getDateDiff($end_date, date("Y-m-d"));
            }
            //备注
            $temp["note"] = $end_date;
            $detail_list[] = $temp;
            $data["detail_list"] = $detail_list;
            return self::purchaseArriveAddImp($data);

        }else{
            return false;
        }
    }

    //采购入库单新增实现
    //gy.erp.new.purchase.arrive.add
    public static function purchaseArriveAddImp ($data) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.new.purchase.arrive.add',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }

    //采购订单批量推送
    public static function purchaseAddByData ($arrfix) {
        $data = array();

        //仓库代码
        $data["warehouse_code"] = self::$warehouse_code;

        //供应商代码
        $data["supplier_code"] = "FCYS";

        //商品列表
        $detail_list = [];

        foreach ($arrfix as $str) {
            if(empty($str)){
                continue;
            }
            $arr = explode(",", $str);
            $sku_code = $arr[0];
            $qty = $arr[1];
            $shopProduct = ShopProductDao::getShopProductBySku_code($sku_code);
            if($shopProduct instanceof ShopProduct){
                $temp = [];
                //商品代码
                $temp["barcode"] = $shopProduct->sku_code;
                //数量
                $temp["qty"] = $qty;

                $stockItem = StockItemDao::getLastByShopProduct($shopProduct);
                $price = $shopProduct->getPrice_yuan();
                if($stockItem instanceof StockItem){
                    $price = $stockItem->getPrice_yuan();
                }
                //价格
                $temp["price"] = $price;
                //有效期 int
                /*if(!empty($end_date)){
                    $temp["shelfLife"] = XDateTime::getDateDiff($end_date, date("Y-m-d"));
                }*/
                //备注
                $temp["note"] = "";
                $detail_list[] = $temp;
            }
        }
        $data["detail_list"] = $detail_list;
        return self::purchaseAddImp($data);
    }


    //采购订单新增
    public static function purchaseAdd ($sku_code, $qty) {
        $data = array();

        //仓库代码
        $data["warehouse_code"] = self::$warehouse_code;

        //供应商代码
        $data["supplier_code"] = "FCYS";

        //商品列表
        $detail_list = [];
        $temp = [];
        $shopProduct = ShopProductDao::getShopProductBySku_code($sku_code);
        if($shopProduct instanceof ShopProduct){
            //商品代码
            $temp["barcode"] = $shopProduct->sku_code;
            //数量
            $temp["qty"] = $qty;

            $stockItem = StockItemDao::getLastByShopProduct($shopProduct);
            $price = $shopProduct->getPrice_yuan();
            if($stockItem instanceof StockItem){
                $price = $stockItem->getPrice_yuan();
            }
            //价格
            $temp["price"] = $price;
            //有效期 int
            /*if(!empty($end_date)){
                $temp["shelfLife"] = XDateTime::getDateDiff($end_date, date("Y-m-d"));
            }*/
            //备注
            $temp["note"] = "";
            $detail_list[] = $temp;
            $data["detail_list"] = $detail_list;
            return self::purchaseAddImp($data);

        }else{
            return false;
        }
    }

    //采购订单新增
    //gy.erp.purchase.add
    public static function purchaseAddImp ($data) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.purchase.add',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }

    //获取中国所有的省
    public static function provincesGet(){
        $result = array();
        $page_no = 1;
        $page_size = 100;
        while($page_no*100 < 6000){
            $data = array();
            $data["page_size"] = $page_size;
            $data["page_no"] = $page_no;
            $aa = GuanYiService::areaGetImp($data);
            $sysAreas = $aa["sysAreas"];
            foreach($sysAreas as $item){
                $pid = $item["pid"];
                $level = $item["level"];

                //if( $pid == 1 && $level == 2){
                //if( $pid > 100000 && $level == 2){
                if( $pid > 100000 && $level == 3){
                    $result[] = $item["name"];
                    //$result[] = $item;
                }
            }
            $page_no++;
        }

        return $result;
    }

    //省市区查询
    //gy.erp.area.get
    public static function areaGetImp ($data = array()) {
    	$arr = array(
            'appkey' => self::$appkey,
            'sessionkey' => self::$sessionkey,
            'method' => 'gy.erp.area.get',
        );

        $arr += $data;
        $arr['sign'] = self::getSign($arr);

    	return self::sendPost(self::$requrl, $arr);
    }


    public static function getSign($arr){
        $json_str = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $str = self::$secret . $json_str . self::$secret;
        return strtoupper(md5($str));
    }

    private static function sendPost($url, $fields){
        //$fields = urldecode(json_encode($fields));
        $fields = json_encode($fields, JSON_UNESCAPED_UNICODE);
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        return json_decode($jsonStr, true);
    }

    public static function getFixProvince($province){
        $arr = ["北京","天津","河北省","山西省","内蒙古自治区","辽宁省","吉林省","黑龙江省","上海",
                "江苏省","浙江省","安徽省","福建省","江西省","山东省","河南省","湖北省","湖南省",
                "广东省","广西壮族自治区","海南省","重庆","四川省","贵州省","云南省","西藏自治区",
                "陕西省","甘肃省","青海省","宁夏回族自治区","新疆维吾尔自治区","台湾","香港特别行政区",
                "澳门特别行政区","海外","新疆","广西","广西壮族自治区省","内蒙古自治区省","新疆维吾尔自治区省",
                "广西壮族自治区省","宁夏回族自治区省","西藏自治区省","西藏","内蒙古","辽宁","广东","贵州","吉林",
                "安徽","海南","云南","甘肃","内蒙","青海","山东","四川","湖北","江西","广西省"];
        foreach($arr as $v){
            if($province == $v){
                return $province;
            }
        }
        return mb_substr($province, 0, mb_strlen($province)-1);
    }
}
