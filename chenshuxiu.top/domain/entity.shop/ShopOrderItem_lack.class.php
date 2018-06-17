<?php

/**
 * Created by Atom.
 * User: Jerry
 * Date: 2018/4/25
 * Time: 9:14
 */
class ShopOrderItem_lack extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'shoporderid'    //shoporderid
        , 'shopproductid'    //shopproductid
        , 'price'    //单价(下单时), 单位分
        , 'cnt'    //数量
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('shoporderid', 'shopproductid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["shoporder"] = array("type" => "ShopOrder", "key" => "shoporderid");
        $this->_belongtos["shopproduct"] = array("type" => "ShopProduct", "key" => "shopproductid");
    }

    // $row = array();
    // $row["shoporderid"] = $shoporderid;
    // $row["shopproductid"] = $shopproductid;
    // $row["price"] = $price;
    // $row["cnt"] = $cnt;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopOrderItem_lack::createByBiz row cannot empty");

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
    public function isNotLack() {
        return $this->shopproduct->canSale($this->cnt);
    }

    public function haveNotice() {
        $shopProductNotice = ShopProductNoticeDao::getByPatientAndShopProduct($this->shoporder->patient, $this->shopproduct);
        return $shopProductNotice instanceof ShopProductNotice;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
