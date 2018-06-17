<?php
// FitPage
// 具体的一个组装页面

// owner by fhw
// create by fhw
// review by sjp 20160628
class FitPage extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'fitpagetplid',  // fitpagetplid
            'code',  // 编码,冗余
            'diseaseid',  // diseaseid
            'doctorid',  // doctorid
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'fitpagetplid',
            'code',
            'diseaseid',
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["fitpagetpl"] = array(
            "type" => "FitPageTpl",
            "key" => "fitpagetplid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["fitpagetplid"] = $fitpagetplid;
    // $row["code"] = $code;
    // $row["diseaseid"] = $diseaseid;
    // $row["doctorid"] = $doctorid;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Fitpage::createByBiz row cannot empty");

        $default = array();
        $default["fitpagetplid"] = 0;
        $default["code"] = '';
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getFitPageItems () {
        return FitPageItemDao::getListByFitPage($this);
    }

    public function getFitPageItemCnt () {
        $arr = FitPageItemDao::getListByFitPage($this);
        return count($arr);
    }

    public function getFitPageItemByCode ($code) {
        return FitPageItemDao::getByFitpageidCode($this->id, $code);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
