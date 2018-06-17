<?php

/*
 * ShopPkgItem
 */

class ShopPkgItem extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'shoppkgid'    //shoppkgid
        , 'shopproductid'    //shopproductid
        , 'price'    //单价(下单时), 单位分
        , 'cnt'    //数量
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('shoppkgid', 'shopproductid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["shoppkg"] = array("type" => "ShopPkg", "key" => "shoppkgid");
        $this->_belongtos["shopproduct"] = array("type" => "ShopProduct", "key" => "shopproductid");
    }

    // $row = array();
    // $row["shoppkgid"] = $shoppkgid;
    // $row["shopproductid"] = $shopproductid;
    // $row["price"] = $price;
    // $row["cnt"] = $cnt;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopPkgItem::createByBiz row cannot empty");

        $default = array();
        $default["shoppkgid"] = 0;
        $default["shopproductid"] = 0;
        $default["price"] = 0;
        $default["cnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
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

    // ====================================
    // ----------- static method ----------
    // ====================================

}
