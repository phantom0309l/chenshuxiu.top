<?php

/*
 * DoctorDiseaseRef
 */
class DoctorDiseaseRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'visit_daycnt'); // 稳定复诊周期,天数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid',
            'diseaseid');
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
    // $row["diseaseid"] = $diseaseid;
    // $row["visit_daycnt"] = $visit_daycnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorDiseaseRef::createByBiz row cannot empty");

        $wxshopid = 0;
        if ($row["diseaseid"] > 0) {
            $wxshop = WxShopDao::getByDiseaseid($row["diseaseid"]);
            $wxshopid = $wxshop->id;
        }

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["visit_daycnt"] = 0;
        $default["wxshopid"] = $wxshopid;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 20170419 TODO by sjp : wxshopid属性已废弃, 监控调用点
    public function getWxShop () {
        return WxShopDao::getByDiseaseid($this->diseaseid);
    }

    // 20170419 TODO by sjp : wxshopid属性已废弃, 监控调用点
    public function getWxShopId () {
        $wxshop = WxShopDao::getByDiseaseid($this->diseaseid);
        return $wxshop->id;
    }

    // 20170419 TODO by sjp : 因为旧接口需要
    public function getOneDoctorWxShopRef () {
        return DoctorWxShopRefDao::getOneByDoctorDisease($this->doctor, $this->disease);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
