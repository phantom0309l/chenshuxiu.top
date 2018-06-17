<?php
/*
 * WxTaskTplItem
 */
class WxTaskTplItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxtasktplid',  // wxtasktplid
            'pos',  // 序号
            'ename',  // 英文名称
            'title',  // 标题
            'brief',  // 摘要
            'content',  // 内容
            'status',  // 状态
            'pictureid',  // pictureid
            'picture1id',  // picture1id
            'picture2id'); // picture2id

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxtasktpl"] = array(
            "type" => "Wxtasktpl",
            "key" => "wxtasktplid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["picture1"] = array(
            "type" => "Picture",
            "key" => "picture1id");
        $this->_belongtos["picture2"] = array(
            "type" => "Picture",
            "key" => "picture2id");
    }

    // $row = array();
    // $row["wxtasktplid"] = $wxtasktplid;
    // $row["pos"] = $pos;
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["pictureid"] = $pictureid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxTaskTplItem::createByBiz row cannot empty");

        $default = array();
        $default["wxtasktplid"] = 0;
        $default["pos"] = '';
        $default["ename"] = '';
        $default["title"] = '';
        $default["brief"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["pictureid"] = 0;
        $default["picture1id"] = 0;
        $default["picture2id"] = 0;

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
