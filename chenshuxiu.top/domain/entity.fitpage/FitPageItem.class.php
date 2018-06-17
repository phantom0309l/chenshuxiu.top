<?php
// FitPageItem
// 页面元素

// owner by fhw
// create by fhw
// review by sjp 20160628
class FitPageItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'fitpageid',  // fitpageid
            'fitpagetplitemid',  // fitpagetplitemid
            'ismust',  // 是否必填
            'code',  // 条目编码,冗余
            'content',  // 配置内容扩展
            'pos',  // 排序
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'fitpageid',
            'fitpagetplitemid',
            'code');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["fitpage"] = array(
            "type" => "FitPage",
            "key" => "fitpageid");
        $this->_belongtos["fitpagetplitem"] = array(
            "type" => "FitPageTplItem",
            "key" => "fitpagetplitemid");
    }

    // $row = array();
    // $row["fitpageid"] = $fitpageid;
    // $row["fitpagetplitemid"] = $fitpagetplitemid;
    // $row["code"] = $code;
    // $row["pos"] = $pos;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "FitPageItem::createByBiz row cannot empty");

        $default = array();
        $default["fitpageid"] = 0;
        $default["fitpagetplitemid"] = 0;
        $default["ismust"] = 1;
        $default["code"] = '';
        $default["content"] = '';
        $default["pos"] = 0;
        $default["remark"] = '';

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
