<?php

/*
 * CallProduct
 */
class CallProduct extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'objtype',  // objtype
            'objid',  // objid
            'title',  // 商品标题
            'title_pinyin',  // 商品标题拼音
            'content',  // 商品介绍
            'price',  // 单价, 单位分
            'market_price',  // 市场原价格, 单位分
            'pack_unit',  // 包装单位
            'pos',  // 序号
            'status', // 状态: 0 下线, 1 上线
            'service_percent'
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");

    }

    // $row = array();
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["price"] = $price;
    // $row["market_price"] = $market_price;
    // $row["pack_unit"] = $pack_unit;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    // $row["service_percent"] = $service_percent;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CallProduct::createByBiz row cannot empty");

        $default = array();
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["title"] = '';
        $default["title_pinyin"] = '';
        $default["content"] = '';
        $default["price"] = 0;
        $default["market_price"] = 0;
        $default["pack_unit"] = '';
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
    public function getStatusDesc () {
        $arr = CtrHelper::getStatus_onlineCtrArray();
        $str = $arr[$this->status];

        if ($this->status) {
            $str = "<span class='green'>{$str}</span>";
        } else {
            $str = "<span class='red'>{$str}</span>";
        }

        return $str;
    }

    public function resetTitle_pinyin () {
        $this->title_pinyin = strtolower(PinyinUtilNew::Word2PY($this->title, ''));
    }

    public function getTitle_p () {
        return strtoupper(substr($this->title_pinyin, 0, 1));
    }

    public function getPrice_yuan () {
        return sprintf("%.2f", $this->price / 100);
    }

    public function getMarket_price_yuan () {
        return sprintf("%.2f", $this->market_price / 100);
    }

    // 是否在线上
    public function isOnline () {
        return 1 == $this->status;
    }
}
