<?php

/*
 * PatientRemarkTpl
 */
class PatientRemarkTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'typestr',  // 类型
            'name',  // 标题
            'pos'); // 排序
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
    // $row["type"] = $type;
    // $row["pos"] = $pos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientRemarkTpl::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["typestr"] = '';
        $default["name"] = '';
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getGroupArr () {
        $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("PatientRemarkTpl", $this->id, $this->typestr);

        $arr = array();
        foreach ($commonwords as $commonword) {
            $arr["{$commonword->groupstr}"][] = $commonword;
        }

        return $arr;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
