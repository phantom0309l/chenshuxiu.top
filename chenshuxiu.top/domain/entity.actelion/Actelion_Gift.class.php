<?php

/*
 * Actelion_Gift
 */
class Actelion_Gift extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //礼品名称
        ,'pictureid'    //礼品图片
        ,'jifen_price'    //礼品价值积分
        ,'remark'    //备注
        ,'left_cnt'    //剩余库存数量
        ,'init_cnt'    //初始库存数量
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'pictureid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["picture"] = array ("type" => "Picture", "key" => "pictureid" );
    }

    // $row = array(); 
    // $row["title"] = $title;
    // $row["pictureid"] = $pictureid;
    // $row["jifen_price"] = $jifen_price;
    // $row["remark"] = $remark;
    // $row["left_cnt"] = $left_cnt;
    // $row["init_cnt"] = $init_cnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Actelion_Gift::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["pictureid"] =  0;
        $default["jifen_price"] =  0;
        $default["remark"] = '';
        $default["left_cnt"] =  0;
        $default["init_cnt"] =  0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 获取标题（obj）
    public function getTitleStr () {
        return $this->title;
    }
}
