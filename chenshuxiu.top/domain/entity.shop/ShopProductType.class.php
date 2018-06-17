<?php

/*
 * ShopProductType
 */
class ShopProductType extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseasegroupid',  // 疾病组id
            'name',  // 类别名称
            'pos'); // 序号
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["diseasegroup"] = array(
            "type" => "DiseaseGroup",
            "key" => "diseasegroupid");
    }

    // $row = array();
    // $row["diseasegroup"] = $diseasegroup;
    // $row["name"] = $name;
    // $row["pos"] = $pos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ShopProductType::createByBiz row cannot empty");

        $default = array();
        $default["diseasegroup"] = 0;
        $default["name"] = '';
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
