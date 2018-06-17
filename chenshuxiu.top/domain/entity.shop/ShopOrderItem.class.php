<?php

/*
 * ShopOrderItem
 */

class ShopOrderItem extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'shoporderid',  // shoporderid
            'shopproductid',  // shopproductid
            'price',  // 单价(下单时), 单位分
            'cnt'); // 数量
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'shoporderid',
            'shopproductid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["shoporder"] = array(
            "type" => "ShopOrder",
            "key" => "shoporderid");

        $this->_belongtos["shopproduct"] = array(
            "type" => "ShopProduct",
            "key" => "shopproductid");
    }

    // $row = array();
    // $row["shoporderid"] = $shoporderid;
    // $row["shopproductid"] = $shopproductid;
    // $row["price"] = $price;
    // $row["cnt"] = $cnt;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopOrderItem::createByBiz row cannot empty");

        $default = array();
        $default["shoporderid"] = 0;
        $default["shopproductid"] = 0;
        $default["price"] = 0;
        $default["cnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 修正单价
    public function fixPrice() {
        // 支付过的订单, 不能修改单价
        if ($this->shoporder->is_pay) {
            return false;
        }

        $this->price = $this->shopproduct->price;
    }

    // 金额 (分)
    public function getAmount() {
        return $this->price * $this->cnt;
    }

    // 金额 (元)
    public function getAmount_yuan() {
        return sprintf("%.2f", $this->getAmount() / 100);
    }

    // 单价 (元)
    public function getPrice_yuan() {
        return sprintf("%.2f", $this->price / 100);
    }

    // 获取服务金额
    public function getService_yuan() {
        $per = $this->shopproduct->service_percent;
        $cnt = $this->cnt;
        $goods_back_cnt = $this->getHasGoodsBackCnt();
        $price = $this->getPrice_yuan();
        $ratio = 1;
        $patient = $this->shoporder->patient;
        if (false == $patient instanceof Patient) {
            return 0;
        }
        if ($this->shoporder->patient->isMenZhenVip()) {
            $ratio = 0.95;
        }

        return ($cnt - $goods_back_cnt) * $price * $per * $ratio;
    }

    // 获取已退货数
    public function getHasGoodsBackCnt() {
        return ShopOrderItemStockItemRefDao::getHasGoodsBackCntByShopOrderItem($this);
    }

    // 获取当前最大可退货数
    public function getMaxGoodsBackCnt() {
        $shopOrder = $this->shoporder;
        if (!$shopOrder->isGoodsOutAll()) {
            return 0;
        }
        $cnt = $this->cnt;
        $has_goods_back_cnt = $this->getHasGoodsBackCnt();

        $max_goods_back_cnt = $cnt - $has_goods_back_cnt;

        if ($max_goods_back_cnt < 0) {
            $max_goods_back_cnt = 0;
        }

        return $max_goods_back_cnt;
    }

    public function getCanPkgCnt() {
        $cnt = ShopPkgItemDao::getCntByShopOrderShopProduct($this->shoporder, $this->shopproduct);

        return $this->cnt - $cnt;
    }

    public function isLack() {
        return false == $this->shopproduct->canSale($this->cnt);
    }
}
