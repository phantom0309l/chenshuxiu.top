<?php

/*
 * DealwithTpl
 */
class DealwithTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseasegroupid',  // diseasegroupid
            'diseaseid',  // diseaseid
            'doctorid',  // doctorid
            'groupstr',  // 分组
            'title',  // title
            'msgcontent',  // msgcontent
            'keywords',  // 关键词,用于匹配患者咨询问题
            'typestr',  // 多动症
            'objtype',  // objtype
            'objid',  // objid
            'objcode',  // objcode
            'sendcnt'); // sendcnt
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["diseasegroup"] = array(
            "type" => "DiseaseGroup",
            "key" => "diseasegroupid");

        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["msgcontent"] = $msgcontent;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DealwithTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseasegroupid"] = 0;
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["groupstr"] = '';
        $default["title"] = '';
        $default["msgcontent"] = '';
        $default["keywords"] = '';
        $default["typestr"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';
        $default["sendcnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTitleFix () {
        $doctor_str = "";
        $disease_str = "";

        if ($this->doctorid > 0) {
            $doctor_str = "{$this->doctor->name}";
        }

        if ($this->diseaseid > 0) {
            $disease_str = "{$this->disease->name}";
        }

        $str = "{$this->title} - {$disease_str} - {$doctor_str}";

        return $str;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
