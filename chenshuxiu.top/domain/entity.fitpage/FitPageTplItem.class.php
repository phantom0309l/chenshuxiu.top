<?php
// FitPageTplItem
// 页面模板的元素

// owner by fhw
// create by fhw
// review by sjp 20160628

class FitPageTplItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'fitpagetplid',  // fitpagetplid
            'code',  // 条目编码
            'name',  // 条目名称
            'content',  // 配置模板内容扩展,备选项
            'pos',  // 排序
            'remark'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'fitpagetplid',
            'code');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["fitpagetpl"] = array(
            "type" => "FitPageTpl",
            "key" => "fitpagetplid");
    }

    // $row = array();
    // $row["fitpagetplid"] = $fitpagetplid;
    // $row["code"] = $code;
    // $row["name"] = $name;
    // $row["pos"] = $pos;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "FitPageTplItem::createByBiz row cannot empty");

        $default = array();
        $default["fitpagetplid"] = 0;
        $default["code"] = '';
        $default["name"] = '';
        $default["type"] = '';
        $default["content"] = '';
        $default["pos"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getFitPageItems () {
        return FitPageItemDao::getListByFitPageTplItem($this);
    }

    public function getFitPageItemCnt () {
        $arr = FitPageItemDao::getListByFitPageTplItem($this);

        return count($arr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
