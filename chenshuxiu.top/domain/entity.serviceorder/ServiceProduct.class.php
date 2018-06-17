<?php

/*
 * ServiceProduct
 */

class ServiceProduct extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'pictureid'    //pictureid, 主图片
        , 'type'    //服务商品类型：quickpass 等
        , 'title'    //服务商品标题
        , 'short_title'    //服务商品标题
        , 'content'    //服务商品介绍
        , 'price'    //服务商品总价格, 单位分
        , 'item_cnt'    //含有的服务项数量，单位根据type来决定
        , 'pos'    //序号
        , 'status'    //状态: 0 下线, 1 上线
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = [];
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["picture"] = array("type" => "Picture", "key" => "pictureid");
    }

    // $row = array(); 
    // $row["pictureid"] = $pictureid;
    // $row["type"] = $type;
    // $row["title"] = $title;
    // $row["short_title"] = $short_title;
    // $row["content"] = $content;
    // $row["price"] = $price;
    // $row["item_cnt"] = $item_cnt;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ServiceProduct::createByBiz row cannot empty");

        $default = array();
        $default["pictureid"] = 0;
        $default["type"] = '';
        $default["title"] = '';
        $default["short_title"] = '';
        $default["content"] = '';
        $default["price"] = 0;
        $default["item_cnt"] = 0;
        $default["pos"] = 0;
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    public static function getTypes() {
        return [
            'quickpass' => '快速通行证'
        ];
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeStr() {
        return self::getTypes()[$this->type];
    }

    public function getStatusStr() {
        return $this->status == 1 ? '上线' : '下线';
    }

    public function getPrice_yuan() {
        return sprintf("%.2f", $this->price / 100);
    }
}
