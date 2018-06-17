<?php

/*
 * ShopProduct
 */

class ShopProduct extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    const JINGLING24_ID = 305494016;
    const JINGLING10_ID = 287697596;

    const JINGLING24_GIFT_ID = 543005116;
    const JINGLING10_GIFT_ID = 543580646;

    const ZHENGDING_ID = 553156686;

    public static function getKeysDefine() {
        return array(
            'shopproducttypeid',  // shopproducttypeid
            'sku_code', //sku_code
            'objtype',  // objtype
            'objid',  // objid
            'is_water', //是否是液体
            'pictureid',  // pictureid, 主图片
            'title',  // 商品标题
            'title_pinyin',  // 商品标题拼音
            'content',  // 商品介绍
            'product_factory', //生产厂家
            'price',  // 单价, 单位分
            'market_price',  // 市场原价格, 单位分
            'pack_unit',  // 包装单位
            'left_cnt', //剩余数量
            'warning_cnt', //库存警戒值，可售库存不可低于警戒值
            'notice_cnt', //库存剩余多少时提醒
            'buy_cnt_init', //初始购买数量
            'buy_cnt_max', //最大购买数量
            'pos',  // 序号
            'status', // 状态: 0 下线, 1 上线
            'service_percent'
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'objid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["shopproducttype"] = array(
            "type" => "ShopProductType",
            "key" => "shopproducttypeid");

        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");

        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["shopproducttypeid"] = $shopproducttypeid;
    // $row["sku_code"] = $sku_code;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["is_water"] = $is_water;
    // $row["pictureid"] = $pictureid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["product_factory"] = $product_factory;
    // $row["price"] = $price;
    // $row["market_price"] = $market_price;
    // $row["pack_unit"] = $pack_unit;
    // $row["left_cnt"] = $left_cnt;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    // $row["service_percent"] = $service_percent;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopProduct::createByBiz row cannot empty");

        $default = array();
        $default["shopproducttypeid"] = 0;
        $default["sku_code"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["is_water"] = 0;
        $default["pictureid"] = 0;
        $default["title"] = '';
        $default["title_pinyin"] = '';
        $default["content"] = '';
        $default["product_factory"] = '';
        $default["price"] = 0;
        $default["market_price"] = 0;
        $default["pack_unit"] = '';
        $default["left_cnt"] = 0;
        $default["warning_cnt"] = 0;
        $default["notice_cnt"] = 5;
        $default["buy_cnt_init"] = 4;
        $default["buy_cnt_max"] = 12;
        $default["pos"] = 0;
        $default["status"] = 0;
        $default["service_percent"] = 0;

        $row += $default;

        $entity = new self($row);
        $entity->resetTitle_pinyin();

        return $entity;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 是否可售
    public function canSale($cnt) {
        $canSale_cnt = $this->getLeft_cntOfReal();
        $warning_cnt = $this->warning_cnt;

        //没有设置警戒值的直接可售
        if ($warning_cnt == 0) {
            return true;
        }

        //可售库存 > 警戒库存
        //患者本次下单量 <= 可售库存
        if ($canSale_cnt > $warning_cnt && $cnt <= $canSale_cnt) {
            return true;
        }
        return false;
    }

    public function getStatusDesc() {
        $arr = CtrHelper::getStatus_onlineCtrArray();
        $str = $arr[$this->status];

        if ($this->status) {
            $str = "<span class='green'>{$str}</span>";
        } else {
            $str = "<span class='red'>{$str}</span>";
        }

        return $str;
    }

    public function resetTitle_pinyin() {
        $this->title_pinyin = strtolower(PinyinUtilNew::Word2PY($this->title, ''));
    }

    public function getTitle_p() {
        return strtoupper(substr($this->title_pinyin, 0, 1));
    }

    //获取生产企业
    public function getCompanyName() {
        $name = $this->product_factory;
        if (empty($name)) {
            $obj = $this->obj;
            if ($obj instanceof MedicineProduct) {
                $name = $obj->company_name;
            }
        }
        return $name;
    }

    public function getPrice_yuan() {
        return sprintf("%.2f", $this->price / 100);
    }

    public function getMarket_price_yuan() {
        return sprintf("%.2f", $this->market_price / 100);
    }

    //获取商品在某个时间点库存金额, 不传默认取当前库存金额
    public function getStockSumPrice_yuan($thedate = "") {
        $today = date("Y-m-d", time());
        if (empty($thedate) || $thedate == $today) {
            $sum_price = StockItemDao::getSumPriceByShopProduct($this);
        } else {
            $sum_price_base = StockItemDao::getSumPriceByShopProduct($this);
            //在[thedate, today]时间段内入库的商品金额
            $amount_in = StockItemDao::getAmountByShopProductStartdateEnddate($this, $thedate, $today);
            //在[thedate, today]时间段内出库的商品金额
            $amount_out = ShopOrderItemStockItemRefDao::getHasGoodsOutCostAmountByShopProductStartdateEnddate($this, $thedate, $today);

            $sum_price = $sum_price_base + $amount_out - $amount_in;
        }
        return sprintf("%.2f", $sum_price);
    }

    //获取商品在某个时间点库存数量, 不传默认取当前库存数
    public function getStockCnt($thedate = "") {
        $today = date("Y-m-d", time());
        if (empty($thedate) || $thedate == $today) {
            $cnt = $this->left_cnt;
        } else {
            $cnt_base = $this->left_cnt;
            //在[thedate, today]时间段内入库的商品数量
            $cnt_in = StockItemDao::getCntByShopProductStartdateEnddate($this, $thedate, $today);
            //在[thedate, today]时间段内出库的商品数量
            $cnt_out = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopProductStartdateEnddate($this, $thedate, $today);

            $cnt = $cnt_base + $cnt_out - $cnt_in;
        }
        return $cnt;
    }

    public function getShopProductPictureCnt() {
        return ShopProductPictureDao::getShopProductPictureCntByShopProduct($this);
    }

    public function getShopProductPictures() {
        return ShopProductPictureDao::getShopProductPicturesByShopProduct($this);
    }

    // 获取幻灯图片
    public function getSlidePictures() {
        $shopProductPictures = $this->getShopProductPictures();
        $arr = array();
        foreach ($shopProductPictures as $a) {
            if ($a->picture instanceof Picture) {
                $arr[] = $a->picture;
            }
        }
        $masterPicture = $this->picture;
        if ($masterPicture instanceof Picture) {
            array_unshift($arr, $masterPicture);
        }
        return $arr;
    }

    // 获取订单项
    public function getShopOrderItemByShopOrder(ShopOrder $shopOrder) {
        foreach ($shopOrder->getShopOrderItems() as $a) {
            if ($a->shopproductid == $this->id) {
                return $a;
            }
        }

        return null;
    }

    // 获取订单中购买的商品数量
    public function getBuyCntByShopOrder($shopOrder) {
        if (false == $shopOrder instanceof ShopOrder) {
            return 0;
        }

        $shopOrderItem = $this->getShopOrderItemByShopOrder($shopOrder);

        if ($shopOrderItem instanceof ShopOrderItem) {
            return $shopOrderItem->cnt;
        }

        return 0;
    }

    // 获取销售数量, 基于shoporderitem的数量统计，退货的数量包含在内
    public function getSaleCnt() {
        return ShopOrderItemDao::getShopOrderItemCntByShopProduct($this);
    }

    // 获取销售概况，基于shoporderitemstockitemrefs, 退货的情况不包含在内
    // 销售概况包括 => 销售数量、销售金额、成本金额
    public function getSaledProfile($startdate, $enddate) {
        return ShopOrderItemStockItemRefDao::getHasGoodsOutSaledProfileByShopProductStartdateEnddate($this, $startdate, $enddate);
    }

    //获取已出库数量
    public function getHasGoodsOutCnt() {
        return StockItemDao::getHasGoodsOutCntByShopProduct($this);
    }

    // 是否在线上
    public function isOnline() {
        return 1 == $this->status;
    }

    // 获取某个商品已支付未出库的数量
    public function getCntOfShopOrderIs_payNotgoodsout() {
        return ShopPkgItemDao::getShopProductSumCntOfShopOrderIs_payNotgoodsout($this);
    }

    //获取真实剩余量
    public function getLeft_cntOfReal() {
        $left_cnt = $this->left_cnt;
        $cnt = $this->getCntOfShopOrderIs_payNotgoodsout();
        return $left_cnt - $cnt;
    }

    //缺货判断
    public function isOutOfStock() {
        $cnt = $this->getLeft_cntOfReal();
        return $cnt <= 0;
    }

    //获取上周销售量
    //来自两个地方：已出库，从shoporderitemstockitemrefs获取；未出库：从shoporderitems获取
    public function getSaleCntOfLastWeek() {
        $thetime = strtotime('last week');
        $startdate = date("Y-m-d", $thetime);
        $enddate = date("Y-m-d", strtotime($startdate) + 86400 * 6);
        $cnt1 = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopProductStartdateEnddate($this, $startdate, $enddate);
        $cnt2 = ShopPkgItemDao::getShopProductSumCntOfShopOrderIs_payNotgoodsoutByStartdateEnddate($this, $startdate, $enddate);
        return $cnt1 + $cnt2;
    }

    //获取提醒运营线值
    //每个药品的提醒运营线 最近60天的销售量的平均值 x 7
    public function getMaybeNoticeCnt() {
        $startdate = date("Y-m-d", time() - 86400 * 60);
        $enddate = date("Y-m-d", time());
        $cnt = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopProductStartdateEnddate($this, $startdate, $enddate);

        return ceil(($cnt / 60) * 7);
    }

    //获取应急库存数
    //应急库存量为平均每单该药销量*10
    public function getEmergentStockCnt() {
        $cnt = ShopOrderItemDao::getShopOrderItemCntOfIs_payByShopProduct($this);
        $sumCnt = ShopOrderItemDao::getShopProductSumCntOfShopOrderIs_pay($this);

        if ($cnt == 0) {
            return 0;
        }

        return ceil(($sumCnt / $cnt) * 10);
    }

    //判断是否是赠品
    public function isGift() {
        $gift_arr = [self::JINGLING24_GIFT_ID, self::JINGLING10_GIFT_ID];
        $id = $this->id;
        if (in_array($id, $gift_arr)) {
            return true;
        } else {
            return false;
        }
    }

    //商品可以附带创建赠品的数量
    public function canCreateGiftCnt($num) {
        if ($num < 1) {
            return 0;
        }
        $gift_cnt = 0;

        //静灵口服液(安生),10ml*24支  买3盒赠1盒逻辑
        if ($this->isJingLing24()) {
            $gift_cnt = floor($num / 3);
        }

        //静灵口服液(安生),10ml*10支  买5盒赠1盒逻辑
        if ($this->isJingLing10()) {
            $gift_cnt = floor($num / 5);
        }

        return $gift_cnt;
    }

    //获取商品的赠品
    public function getGiftShopProduct() {
        $obj = null;
        //静灵口服液(安生),10ml*24支
        if ($this->isJingLing24()) {
            $obj = ShopProduct::getById(self::JINGLING24_GIFT_ID);
        }

        //静灵口服液(安生),10ml*10支
        if ($this->isJingLing10()) {
            $obj = ShopProduct::getById(self::JINGLING10_GIFT_ID);
        }

        return $obj;
    }

    //是否是静灵口服液(安生),10ml*24支
    public function isJingLing24() {
        $id = $this->id;
        return $id == self::JINGLING24_ID;
    }

    //是否是静灵口服液(安生),10ml*10支
    public function isJingLing10() {
        $id = $this->id;
        return $id == self::JINGLING10_ID;
    }

    //是液体商品
    public function isWater() {
        return 1 == $this->is_water;
    }

}
