<?php

/*
 * ShopProductPicture
 */
class ShopProductPicture extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'shopproductid',  // shopproductid
            'pictureid',  // pictureid
            'pos'); // 序号
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'shopproductid',
            'pictureid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["shopproduct"] = array(
            "type" => "ShopProduct",
            "key" => "shopproductid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["shopproductid"] = $shopproductid;
    // $row["pictureid"] = $pictureid;
    // $row["pos"] = $pos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ShopProductPicture::createByBiz row cannot empty");

        $default = array();
        $default["shopproductid"] = 0;
        $default["pictureid"] = 0;
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
