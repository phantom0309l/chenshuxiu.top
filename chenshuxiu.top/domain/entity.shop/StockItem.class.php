<?php
/*
 * StockItem
 */
class StockItem extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'shopproductid'    //shopproductid
        ,'price'    //单价, 单位分
        ,'cnt'    //入库数
        ,'left_cnt'    //剩余数量
        ,'batch_number'    //生成批号
        ,'in_time'    //入库时间
        ,'expire_date'    //过期时间
        ,'sourse'    //渠道，来源
        ,'order_person'    //订货人
        ,'pay_person'    //付款人
        ,'the_date'    //账期
        ,'pay_type'    //付款方式 0:未知；1:电汇；2:支付宝；3:微信
        ,'has_invoice'    //有无发票 0：无；1:有。
        ,'auditorid'    //auditorid
        ,'remark'    //运营备注
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('shopproductid', 'auditorid', 'price');
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["shopproduct"] = array ("type" => "ShopProduct", "key" => "shopproductid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["shopproductid"] = $shopproductid;
    // $row["price"] = $price;
    // $row["cnt"] = $cnt;
    // $row["left_cnt"] = $left_cnt;
    // $row["batch_number"] = $batch_number;
    // $row["in_time"] = $in_time;
    // $row["expire_date"] = $expire_date;
    // $row["sourse"] = $sourse;
    // $row["auditorid"] = $auditorid;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"StockItem::createByBiz row cannot empty");

        $default = array();
        $default["shopproductid"] =  0;
        $default["price"] =  0;
        $default["cnt"] =  0;
        $default["left_cnt"] =  0;
        $default["batch_number"] = '';
        $default["in_time"] = '';
        $default["expire_date"] = '';
        $default["sourse"] = '';
        $default["order_person"] = '';
        $default["pay_person"] = '';
        $default["the_date"] = '0000-00-00';
        $default["pay_type"] = 0;
        $default["has_invoice"] = 0;
        $default["auditorid"] =  0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getPrice_yuan () {
        return sprintf("%.2f", $this->price / 100);
    }

    public function getPayTypeStr () {
        $arr = self::getPayTypeArr();
        return $arr[$this->pay_type];
    }

    public function getHasInvoiceStr () {
        $arr = self::getHasInvoiceArr();
        return $arr[$this->has_invoice];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    public static function getPayTypeArr () {
        $arr = array();
        $arr[1] = "电汇";
        $arr[2] = "支付宝";
        $arr[3] = "微信";
        return $arr;
    }

    public static function getHasInvoiceArr () {
        $arr = array();
        $arr[0] = "无";
        $arr[1] = "有";
        return $arr;
    }
}
