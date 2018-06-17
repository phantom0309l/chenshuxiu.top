<?php
// FitPageTpl
// 可组装页面模板

// owner by fhw
// create by fhw
// review by sjp 20160628

class FitPageTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'code',  // 编码
            'name',  // 页面模板名称
            'remark'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'code');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    }

    // $row = array();
    // $row["code"] = $code;
    // $row["name"] = $name;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "FitPageTpl::createByBiz row cannot empty");

        $default = array();
        $default["code"] = '';
        $default["name"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getFitPageTplItems () {
        return FitPageTplItemDao::getListByFitPageTpl($this);
    }

    public function getFitPageTplItemCnt () {
        $arr = FitPageTplItemDao::getListByFitPageTpl($this);
        return count($arr);
    }

    public function getFitPages () {
        return FitPageDao::getListByFitPageTpl($this);
    }

    public function getFitPageCnt () {
        $arr = FitPageDao::getListByFitPageTpl($this);
        return count($arr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
