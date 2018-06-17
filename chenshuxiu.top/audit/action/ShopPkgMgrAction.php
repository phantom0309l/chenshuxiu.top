<?php

class ShopPkgMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $shoppkgid = XRequest::getValue("shoppkgid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($shoppkgid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $shoppkgid;
        }

        //获得实体
        $sql = "select *
                    from shoppkgs
                    where 1 = 1 {$cond} order by id desc";
        $shopPkgs = Dao::loadEntityList4Page("ShopPkg", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopPkgs", $shopPkgs);

        //获得分页
        $countSql = "select count(*)
                    from shoppkgs
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shoppkgmgr/list?shoppkgid={$shoppkgid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("shoppkgid", $shoppkgid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne() {
        $shopPkgId = XRequest::getValue("shoppkgid", 0);

        $shopPkg = ShopPkg::getById($shopPkgId);

        XContext::setValue("shopPkg", $shopPkg);
        return self::SUCCESS;
    }

    // 电子运单列表
    public function doEorderList() {
        $mydisease = $this->mydisease;
        $pagesize = XRequest::getValue("pagesize", 5);
        $pagenum = XRequest::getValue("pagenum", 1);

        //满足以下条件
        //1、未发货
        //2、已支付
        //3、未退款+部分退款
        //4、顺丰
        //5、非测试用户
        $cond = " and a.is_sendout = 0 and b.is_pay = 1 and b.amount > b.refund_amount and a.express_company = '顺丰' and (a.userid > 20000 or a.userid < 10000) ";
//        $cond = " and a.is_sendout = 0 and b.amount > b.refund_amount and a.express_company = '顺丰' ";

        if ($mydisease instanceof Disease) {
            $cond .= " and d.diseaseid = :diseaseid ";
            $bind[":diseaseid"] = $mydisease->id;
        }

        //获得实体
        $sql = "select distinct a.*
                    from shoppkgs a
                    inner join shoporders b on b.id=a.shoporderid
                    inner join doctors c on c.id = b.the_doctorid
                    inner join doctordiseaserefs d on d.doctorid = c.id
                    where 1 = 1 {$cond} order by b.time_pay asc, a.id asc";
        $shopPkgs = Dao::loadEntityList4Page("ShopPkg", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopPkgs", $shopPkgs);

        //获得分页
        $countSql = "select count(distinct a.id)
                    from shoppkgs a
                    inner join shoporders b on b.id=a.shoporderid
                    inner join doctors c on c.id = b.the_doctorid
                    inner join doctordiseaserefs d on d.doctorid = c.id
                    where 1 = 1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shoppkgmgr/eorderlist";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    //生成电子运单
    public function doCreateEOrderJson() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $shopPkg = ShopPkg::getById($shopPkgId);

        DBC::requireNotTrue($shopPkg->is_push_erp, "已经推送到erp系统的不能生成电子运单");

        //请求电子运单已成功过，再次访问打印
        $eorder_content = $shopPkg->eorder_content;
        if (!empty($eorder_content)) {
            $data = [];
            $data["errno"] = 0;
            $data["has_visited"] = true;
            $data["express_no"] = $shopPkg->express_no;
            XContext::setValue('json', $data);
            return self::TEXTJSON;
        }

        $isFillExpress_no = $shopPkg->isFillExpress_no();
        DBC::requireNotTrue($isFillExpress_no, "不能重复生成电子运单");

        DBC::requireTrue("顺丰" == $shopPkg->express_company, "暂不支持顺丰之外的电子运单打印");

        //构造电子面单提交信息
        $eorder = [];
        $eorder["ShipperCode"] = "SF";
        $eorder["OrderCode"] = $shopPkg->id;
        $eorder["PayType"] = 3; //邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
        $eorder["ExpType"] = 1; //快递类型：1-标准快件
        $eorder["IsReturnPrintTemplate"] = 1; //返回电子面单模板：0-不需要；1-需要
        $eorder["IsNotice"] = 1; //是否通知快递员上门揽件：0-通知；1-不通知；不填则默认为0
        $eorder["CustomerName"] = "0100026792";
        $eorder["MonthCode"] = "0100026792"; //月结编码

        //发件人
        $sender = [];
        $sender["Name"] = "方寸医生";
        $sender["Tel"] = "010-60643332";
        $sender["Mobile"] = "18510542099";
        $sender["ProvinceName"] = "北京市";
        $sender["CityName"] = "西城区";
        $sender["ExpAreaName"] = "";
        $sender["Address"] = "华远北街通港大厦708";

        $shopaddress = $shopPkg->shoporder->shopaddress;

        //收件人
        $receiver = [];
        $receiver["Name"] = $shopaddress->linkman_name;
        $receiver["Mobile"] = $shopaddress->linkman_mobile;
        $receiver["ProvinceName"] = $shopaddress->xprovince->name;
        $receiver["CityName"] = $shopaddress->xcity->name;
        $receiver["ExpAreaName"] = $shopaddress->xcounty->name;
        $receiver["Address"] = $shopaddress->content;

        $commodityOne = [];
        $commodityOne["GoodsName"] = "商品";
        $commodity = [];
        $commodity[] = $commodityOne;

        $eorder["Sender"] = $sender;
        $eorder["Receiver"] = $receiver;
        $eorder["Commodity"] = $commodity;

        //代收货款[COD]
        /*$addservice = [];
        $addserviceOne = [];
        $addserviceOne["Name"] = "COD";
        $addserviceOne["Value"] = $shopOrder->getAmount_yuan();
        $addserviceOne["CustomerID"] = "0100026792";
        $addservice[] = $addserviceOne;
        $eorder["AddService"] = $addservice;*/

        $kdniaoservice = new KdniaoService();
        $data = $kdniaoservice->getEOrderData($eorder);
        $data["has_visited"] = false;
        $data["medicineStr"] = $shopPkg->getTitleAndCntOfShopProducts('<br/>');
        $data["linkmanName"] = $shopaddress->linkman_name;

        $ResultCode = $data["ResultCode"];
        if (!empty($ResultCode) && $ResultCode == "100") {
            $o = $data["Order"];
            $LogisticCode = $o["LogisticCode"];
            $shopPkg->express_no = $LogisticCode;

            //保存eorder_content
            $eorder_content_arr = [];
            $eorder_content_arr["EBusinessID"] = $data["EBusinessID"];
            $eorder_content_arr["Order"] = $o;
            $shopPkg->eorder_content = json_encode($eorder_content_arr, JSON_UNESCAPED_UNICODE);
            $data["express_no"] = $shopPkg->express_no;
            $data["errno"] = 0;
        } else {
            $data["errno"] = -1;
        }
        XContext::setValue('json', $data);
        return self::TEXTJSON;

    }

    //生成电子运单
    public function doCreateEOrderHtml() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $shopPkg = ShopPkg::getById($shopPkgId);
        $shopaddress = $shopPkg->shoporder->shopaddress;

        //请求电子运单已成功过，再次访问打印
        $eorder_content = $shopPkg->eorder_content;
        $data = json_decode($eorder_content, true);
        $eorder = $data["Order"];
        XContext::setValue('eorder', $eorder);
        XContext::setValue('shopaddress', $shopaddress);
        XContext::setValue('shopPkg', $shopPkg);
        return self::SUCCESS;
    }

    // 拆单html
    public function doDivideHtml() {
        $shopPkgId = XRequest::getValue("shoppkgid", 0);
        $shopPkgNum = XRequest::getValue("shoppkgnum", 2);

        $shopPkg = ShopPkg::getById($shopPkgId);

        $shopPkgItems = ShopPkgItemDao::getListByShopPkg($shopPkg);

        XContext::setValue("shopPkg", $shopPkg);
        XContext::setValue("shopPkgItems", $shopPkgItems);
        XContext::setValue("shopPkgNum", $shopPkgNum);
        return self::SUCCESS;
    }

    // 拆单
    public function doDivideJson() {
        $shopPkgNum = XRequest::getValue("shoppkgnum", 0);
        $shopPkgId = XRequest::getValue("shoppkgid", 0);
        $dataArr = XRequest::getValue("shopproduct", []);

        DBC::requireTrue(1 < $shopPkgNum, '至少拆分为1单以上！');

        $shopPkgOld = ShopPkg::getById($shopPkgId);
        DBC::requireTrue($shopPkgOld instanceof ShopPkg, '配送单不存在！');

        DBC::requireTrue($shopPkgOld->canChange(), '此配送单，不可拆分！');

        $shopOrder = $shopPkgOld->shoporder;
        DBC::requireTrue($shopOrder instanceof ShopOrder, '订单不存在！');

        // 拆单
        ShopPkgService::divide($shopPkgOld, $shopPkgNum, $dataArr);

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }

    // 订单修改配送费
    public function doExpressModifyPost() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $express_company = XRequest::getValue('express_company', '');
        $express_no = XRequest::getValue('express_no', '');
        $invoice_no = XRequest::getValue('invoice_no', '');
        $express_price_real = XRequest::getValue('express_price_real', 0);

        $shopPkg = ShopPkg::getById($shopPkgId);

        $old_express_price_real = $shopPkg->express_price_real;

        $shopPkg->express_company = $express_company;
        $shopPkg->express_no = $express_no;
        $shopPkg->express_price_real = $express_price_real * 100;

        $shopOrder = $shopPkg->shoporder;
        $shopOrder->invoice_no = $invoice_no;
        $shopOrder->express_price_real -= $old_express_price_real;
        $shopOrder->express_price_real += $express_price_real * 100;

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shopPkg->shoporderid}&preMsg=" . urlencode("配送信息已修改"));

        return self::SUCCESS;
    }

    // 设置为已出库
    public function doSetIs_goodsoutPost() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $is_goodsout = XRequest::getValue('is_goodsout', 1);

        $str = "状态已设置为已出库";
        $shopPkg = ShopPkg::getById($shopPkgId);
        if ($shopPkg->is_goodsout) {
            XContext::setJumpPath("/shopordermgr/one?shoporderid={$shopPkg->shoporderid}&preMsg=" . urlencode($str));
            return self::SUCCESS;
        }

        if (true == $shopPkg->checkStock()) {
            //出库
            $shopPkg->goodsOut();
        } else {
            $str = "因库存不足出库失败";
        }

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shopPkg->shoporderid}&preMsg=" . urlencode($str));

        return self::SUCCESS;
    }

    // 设置为已发货
    public function doSetIs_sendoutPost() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $is_sendout = XRequest::getValue('is_sendout', 1);

        $shopPkg = ShopPkg::getById($shopPkgId);
        $shopPkg->is_sendout = $is_sendout;

        $str = "";
        if ($is_sendout) {

            $shopPkg->time_sendout = XDateTime::now();

            $str = "状态已设置为已发货";
            //发送快递信息
            if (false == $shopPkg->needPushErp()) {
                ExpressService::sendExpress_no($shopPkg);
            }

            //物流信息订阅

        } else {
            $str = "状态已设置为未发货";
        }

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shopPkg->shoporderid}&preMsg=" . urlencode($str));

        return self::SUCCESS;
    }

    // 设置出库并发货
    public function doSetIs_goodsoutAndIs_sendoutJson() {
        $this->result['errno'] = 0;
        $this->result['errmsg'] = '';
        $this->result['data'] = '';

        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $shopPkg = ShopPkg::getById($shopPkgId);

        if ($shopPkg->is_goodsout) {
            $this->result['errno'] = -1;
            $this->result['errmsg'] = "状态已设置为已出库";
            XContext::setValue('json', $this->result);
            return self::TEXTJSON;
        }

        if ($shopPkg->is_sendout) {
            $this->result['errno'] = -2;
            $this->result['errmsg'] = "状态已设置为已发货";
            XContext::setValue('json', $this->result);
            return self::TEXTJSON;
        }


        //出库
        if (true == $shopPkg->checkStock()) {
            $shopPkg->goodsOut();
        } else {
            $this->result['errno'] = -3;
            $this->result['errmsg'] = "因库存不足出库失败";
            XContext::setValue('json', $this->result);
            return self::TEXTJSON;
        }

        //发货
        $shopPkg->is_sendout = 1;
        $shopPkg->time_sendout = XDateTime::now();


        //发送快递信息
        if (false == $shopPkg->needPushErp()) {
            ExpressService::sendExpress_no($shopPkg);
        }

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }

    // 生成erp订单
    public function doTradeAddJson() {
        $this->result['errno'] = 0;
        $this->result['errmsg'] = '';
        $this->result['data'] = '';

        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $shopPkg = ShopPkg::getById($shopPkgId);

        $isBalance = ShopOrderService::isBalance($shopPkg->shoporder);
        DBC::requireTrue($isBalance, "手动推送配送单到erp时，订单【shoporderid{$shopPkg->shoporderid}】的商品没有完全分配到配送单！！！");

        if (false == $shopPkg->canPushErp()) {
            $this->result['errno'] = -1;
            $this->result['errmsg'] = "shopPkg[{$shopPkgId}]，不能推送到ERP";
            XContext::setValue('json', $this->result);
            return self::TEXTJSON;
        }

        $result = GuanYiService::tradeAddByShopPkg($shopPkg);
        $descstr = json_encode($result, JSON_UNESCAPED_UNICODE);
        Debug::trace("===[{$descstr}]====");
        $success = $result["success"];

        if ($success) {
            $shopPkg->is_push_erp = 1;
            $shopPkg->time_push_erp = date("Y-m-d H:i:s");
            $shopPkg->remark_push_erp = "";
        } else {
            $errorDesc = $result["errorDesc"];
            $shopPkg->remark_push_erp = $errorDesc;
            $this->result['errno'] = -2;
            $this->result['errmsg'] = "shopPkg[{$shopPkgId}]，推送到ERP失败";
        }

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }

    public function doDeleteJson() {
        $shopPkgId = XRequest::getValue('shoppkgid', 0);
        $shopPkg = ShopPkg::getById($shopPkgId);
        DBC::requireTrue($shopPkg instanceof ShopPkg, "未找到shoppkg，【shoppkgid={$shopPkgId}】");

        DBC::requireTrue($shopPkg->canChange(), '此配送单，不可删除！');

        $shopOrder = $shopPkg->shoporder;

        DBC::requireTrue($shopOrder instanceof ShopOrder, "没有找到对应的订单【shoppkgid={$shopPkgId}】");

        $shopPkgs = $shopOrder->getShopPkgs();
        array_splice($shopPkgs, array_search($shopPkg, $shopPkgs), 1);
        ShopPkgService::reCalcuExpressPrice($shopOrder, $shopPkgs);

        ShopPkgService::deleteShopPkg($shopPkg);

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }
}
