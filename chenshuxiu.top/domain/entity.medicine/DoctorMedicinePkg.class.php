<?php
// DoctorMedicinePkg
// 医生-基本药方

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class DoctorMedicinePkg extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  //
            'diseaseid',  //
            'name',  // 套餐名称
            'pos'); // pos
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["name"] = $name;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorMedicinePkg::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["name"] = '';
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 包含药品数目
    public function getItemCnt () {
        $items = $this->getItemList();
        return count($items);
    }

    // getItemList
    public function getItemList () {
        return DoctorMedicinePkgItemDao::getListByDoctormedicinepkgid($this->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
