<?php

/*
 * DiseasepaperRef
 */
class DiseasePaperTplRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'papertplid',  //
            'diseaseid',  //
            'doctorid',  //
            'show_in_wx',  // 是否微信端显示
            'show_in_audit',  // 是否在后台显示
            'pos'); // 序号
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'paperid',
            'diseaseid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["papertpl"] = array(
            "type" => "PaperTpl",
            "key" => "papertplid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["papertplid"] = $papertplid;
    // $row["diseaseid"] = $diseaseid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DiseasePaperTplRef::createByBiz row cannot empty");

        $default = array();
        $default["papertplid"] = 0;
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["pos"] = 0;
        $default["show_in_wx"] = 0;
        $default["show_in_audit"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function haveRef_nulldoctorid () {
        $diseasePaperTplRef = $this->getDiseasePaperTplRef_nulldoctorid();
        return $diseasePaperTplRef instanceof DiseasePaperTplRef;
    }

    public function getDiseasePaperTplRef_nulldoctorid () {
        return DiseasePaperTplRefDao::getByDiseaseAndPaperTpl($this->disease, $this->papertpl);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
