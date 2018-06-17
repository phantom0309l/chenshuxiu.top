<?php

/*
 * ShopPkg
 */

class ShopPkg extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'shoporderid'    //shoporderid
        , 'fangcun_platform_no'    //配送单号（方寸平台对外提供的配送单号）
        , 'express_price'    //运费（配送费，单位分）
        , 'express_price_real'    //实际运费（实际真实配送费，单位分）
        , 'is_goodsout'    //是否出库（0：未出库 1：已出库）
        , 'is_sendout'    //是否发货（0：未发货 1：已经发货）
        , 'express_company'    //快递公司
        , 'express_no'    //快递号
        , 'time_goodsout'    //出库时间
        , 'time_sendout'    //发货时间
        , 'eorder_content'    //电子运单接口成功后返回信息
        , 'need_push_erp'    //是否需要推送到erp（0：不推送  1：推送）
        , 'is_push_erp'    //是否已推送到erp（0：未推送 1：已推送）
        , 'time_push_erp'    //推送到erp的时间
        , 'remark_push_erp'    //推送remark
        , 'status'    //状态（0：无效 1：有效）
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('wxuserid', 'userid', 'patientid', 'shoporderid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["shoporder"] = array("type" => "ShopOrder", "key" => "shoporderid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["shoporderid"] = $shoporderid;
    // $row["fangcun_platform_no"] = $fangcun_platform_no;
    // $row["express_price_real"] = $express_price_real;
    // $row["is_goodsout"] = $is_goodsout;
    // $row["is_sendout"] = $is_sendout;
    // $row["express_company"] = $express_company;
    // $row["express_no"] = $express_no;
    // $row["time_goodsout"] = $time_goodsout;
    // $row["time_sendout"] = $time_sendout;
    // $row["eorder_content"] = $eorder_content;
    // $row["need_push_erp"] = $need_push_erp;
    // $row["is_push_erp"] = $is_push_erp;
    // $row["time_push_erp"] = $time_push_erp;
    // $row["remark_push_erp"] = $remark_push_erp;
    // $row["status"] = $status;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopPkg::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["shoporderid"] = 0;
        $default["fangcun_platform_no"] = '';
        $default["express_price"] = 0;
        $default["express_price_real"] = 0;
        $default["is_goodsout"] = 0;
        $default["is_sendout"] = 0;
        $default["express_company"] = '';
        $default["express_no"] = '';
        $default["time_goodsout"] = '0000-00-00 00:00:00';
        $default["time_sendout"] = '0000-00-00 00:00:00';
        $default["eorder_content"] = '';
        $default["need_push_erp"] = 0;
        $default["is_push_erp"] = 0;
        $default["time_push_erp"] = '0000-00-00 00:00:00';
        $default["remark_push_erp"] = '';
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function canChange() {
        return 0 == $this->is_goodsout && 0 == $this->is_sendout && 0 == $this->is_push_erp;
    }

    // 是否可打印顺丰电子运单
    public function canPrintSFEOrder() {
        return ("顺丰" == $this->express_company);
    }

    //是否顺丰派送
    public function isSF() {
        return ("顺丰" == $this->express_company);
    }

    //能够推送订单到ERP
    public function canPushErp() {
        $userid = $this->userid;
        if ($userid > 10000 && $userid < 20000) {
            return false;
        }
        return (1 == $this->need_push_erp) && (0 == $this->is_push_erp) && (0 == $this->is_goodsout) && (0 == $this->is_sendout) && (1 == $this->status) && (0 == $this->shoporder->refund_amount);
    }

    public function checkStock() {
        $temp = array();
        foreach ($this->getShopPkgItems() as $shopPkgItem) {
            $cnt = $shopPkgItem->cnt;
            $shopproduct = $shopPkgItem->shopproduct;
            if ($cnt > $shopproduct->left_cnt) {
                $temp[] = $shopproduct->id;
                $content = "patientid[{$this->patient->id}][{$this->patient->name}]购买[{$shopproduct->title}]时缺货，请及时补货";
                PushMsgService::sendMsgToAuditorBySystem('ShopOrder', 1, $content);
            }
        }
        if (count($temp) > 0) {
            return false;
        }
        return true;
    }

    public function checkStockInTpl() {
        $temp = array();
        foreach ($this->getShopPkgItems() as $shopPkgItem) {
            $cnt = $shopPkgItem->cnt;
            $shopproduct = $shopPkgItem->shopproduct;
            if ($cnt > $shopproduct->left_cnt) {
                $temp[] = $shopproduct->id;
            }
        }
        if (count($temp) > 0) {
            return false;
        }
        return true;
    }

    public function getAmount_yuan() {
        $amount = 0;
        $shopPkgItems = ShopPkgItemDao::getListByShopPkg($this);
        foreach ($shopPkgItems as $shopPkgItem) {
            $amount += $shopPkgItem->price * $shopPkgItem->cnt;
        }
        $amount += $this->express_price;
        return sprintf("%.2f", $amount / 100);
    }

    // 快递费 (元)
    public function getExpress_price_yuan() {
        return sprintf("%.2f", $this->express_price / 100);
    }

    // 实际运费 (元)
    public function getExpress_price_real_yuan() {
        return sprintf("%.2f", $this->express_price_real / 100);
    }

    // 是否出库
    public function getIs_goodsoutStr() {
        return 0 == $this->is_goodsout ? '未出库' : '已出库';
    }

    // 是否发货
    public function getIs_sendoutStr() {
        if (!$this->isValid()) {
            return '--';
        }
        return $this->is_sendout ? '<span class="green">已发货</span>' : '<span class="red">待发货</span>';
    }

    // 快递号
    public function getExpress_noStr() {
        return $this->express_no == "" ? '--' : $this->express_no;
    }

    // 快递公司
    public function getExpress_companyStr() {
        return $this->express_company;
    }

    // 是否需要推送到erp
    public function getNeed_push_erpStr() {
        return 0 == $this->need_push_erp ? '否' : '是';
    }

    // 是否已推送到erp
    public function getIs_push_erpStr() {
        return 0 == $this->need_push_erp ? '否' : '是';
    }

    // 状态
    public function getStatusStr() {
        return 0 == $this->status ? '无效' : '有效';
    }

    //配送单明细
    public function getShopPkgItems() {
        return ShopPkgItemDao::getListByShopPkg($this);
    }

    // 获取商品title 包括商品数量
    public function getTitleAndCntOfShopProducts($split = "\n") {
        $title_arr = [];
        foreach ($this->getShopPkgItems() as $a) {
            $shopProduct = $a->shopproduct;
            $cnt = $a->cnt;
//            $back_cnt = $a->getHasGoodsBackCnt();
//            $cnt = $cnt - $back_cnt;
            $title_arr[] = $shopProduct->title . "[{$cnt}]";
        }
        return implode($split, $title_arr);
    }

    // 出库
    public function goodsOut() {
        // 已出库不能重复出库
        if ($this->is_goodsout) {
            return;
        }

        $isBalance = ShopOrderService::isBalance($this->shoporder);
        DBC::requireTrue($isBalance, "出库时，订单【shoporderid{$this->shoporderid}】的商品没有完全分配到配送单！！！");

        foreach ($this->getShopPkgItems() as $shopPkgItem) {
            $cnt = $shopPkgItem->cnt;
            if ($cnt < 1) {
                continue;
            }
            ShopProductService::goodsOut($shopPkgItem);
        }

        $this->is_goodsout = 1;
        $this->time_goodsout = XDateTime::now();
    }

    //订单是否需要推送erp设置
    public function need_push_erpSet($shopPkgItems) {
        foreach ($shopPkgItems as $shopPkgItem) {
            $sku_code = $shopPkgItem->shopproduct->sku_code;
            if (empty($sku_code)) {
                $this->need_push_erp = 0;
                return;
            }
        }
        $this->need_push_erp = 1;
    }

    public function isFillExpress_no() {
        return "" != $this->express_no;
    }

    public function needPushErp() {
        return 1 == $this->need_push_erp;
    }

    public function isValid() {
        return $this->shoporder->is_pay && ($this->shoporder->getLeft_amount() > 0) && ($this->status == 1);
    }

    //添加赠品
    public function tryAddGift($shopPkgItems) {
        $shopPkg = $this;
        $shopProductIds = [];
        foreach ($shopPkgItems as $shopPkgItem) {
            $shopProductIds[] = $shopPkgItem->shopproductid;
        }
        foreach ($shopPkgItems as $shopPkgItem) {
            $cnt = $shopPkgItem->cnt;
            if ($cnt < 1) {
                continue;
            }
            $shopProduct = $shopPkgItem->shopproduct;

            //如果是赠品跳过
            $is_gift = $shopProduct->isGift();
            if ($is_gift) {
                continue;
            }

            //商品可以附带创建赠品的数量
            $gift_cnt = $shopProduct->canCreateGiftCnt($cnt);
            if ($gift_cnt >= 1) {
                $gift_shopProduct = $shopProduct->getGiftShopProduct();
                if ($gift_shopProduct instanceof ShopProduct) {
                    $gift_shopPkgItem = ShopPkgItemDao::getByShopPkgShopProduct($shopPkg, $gift_shopProduct);
                    if (false == $gift_shopPkgItem instanceof ShopPkgItem && false == in_array($gift_shopProduct->id, $shopProductIds)) {
                        //生成赠品shoppkgitem
                        $row = array();
                        $row["shoppkgid"] = $shopPkg->id;
                        $row["shopproductid"] = $gift_shopProduct->id;
                        $row["price"] = 0;
                        $row["cnt"] = $gift_cnt;
                        ShopPkgItem::createByBiz($row);
                    }
                }
            }
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
