<?php
/*
 * ShopOrderPicture
 */
class ShopOrderPicture extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    // 身份证类型
    const type_idcard = 'idcard';

    public static function getKeysDefine()
    {
        return  array(
        'shoporderid'    //shoporderid
        ,'pictureid'    //pictureid
        ,'type'    //类型,idcard等
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('shoporderid', 'pictureid',);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["shoporder"] = array ("type" => "ShopOrder", "key" => "shoporderid" );
        $this->_belongtos["picture"] = array ("type" => "Picture", "key" => "pictureid" );
    }

    // $row = array();
    // $row["shoporderid"] = $shoporderid;
    // $row["pictureid"] = $pictureid;
    // $row["type"] = $type;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"ShopOrderPicture::createByBiz row cannot empty");

        $default = array();
        $default["shoporderid"] =  0;
        $default["pictureid"] =  0;
        $default["type"] = ShopOrderPicture::type_idcard;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function isIdcard () {
        return ShopOrderPicture::type_idcard == $this->type;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
