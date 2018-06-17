<?php
/*
 * ShopOrderItemStockItemRef
 */
class ShopOrderItemStockItemRef extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'shoporderitemid'    //shoporderitemid
        ,'stockitemid'    //stockitemid
        ,'cnt'    //cnt
        ,'is_recycle'    //是否要回库存: 0 不回, 1 回
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('shoporderitemid' ,'stockitemid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["shoporderitem"] = array ("type" => "ShopOrderItem", "key" => "shoporderitemid" );
        $this->_belongtos["stockitem"] = array ("type" => "StockItem", "key" => "stockitemid" );
    }

    // $row = array();
    // $row["shoporderitemid"] = $shoporderitemid;
    // $row["stockitemid"] = $stockitemid;
    // $row["cnt"] = $cnt;
    // $row["is_recycle"] = $is_recycle;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"ShopOrderItemStockItemRef::createByBiz row cannot empty");

        $default = array();
        $default["shoporderitemid"] =  0;
        $default["stockitemid"] =  0;
        $default["cnt"] =  0;
        $default["is_recycle"] =  0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
