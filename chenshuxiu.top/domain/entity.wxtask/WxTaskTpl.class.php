<?php
/*
 * WxTaskTpl
 */
class WxTaskTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'ename',  // 英文名称
            'title',  // 标题
            'brief',  // 摘要
            'content',  // 内容
            'status',  // 状态
            'pictureid'); // pictureid

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["pictureid"] = $pictureid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxTaskTpl::createByBiz row cannot empty");

        $default = array();
        $default["ename"] = '';
        $default["title"] = '';
        $default["brief"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["pictureid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getWxTaskTplItemCnt () {
        $rows = WxTaskTplItemDao::getListBy($this->id);
        return count($rows);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
