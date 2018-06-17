<?php

/*
 * PrescriptionItem 电子处方明细
 */
class PrescriptionItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'prescriptionid',  // prescriptionid
            'medicineproductid',  // medicineproductid
            'medicine_title',  // 药品标题摘要,备份
            'size_pack',  // 包装规格,备份
            'pack_unit',  // 包装单位,备份
            'drug_way',  // 给药途径,口服|输液等
            'drug_dose',  // 单次用药剂量
            'drug_frequency',  // 用药频率
            'cnt',  // 数量
            'content'); // 本药品服用说明
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'prescriptionid',
            'medicineproductid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["prescription"] = array(
            "type" => "Prescription",
            "key" => "prescriptionid");
        $this->_belongtos["medicineproduct"] = array(
            "type" => "MedicineProduct",
            "key" => "medicineproductid");
    }

    // $row = array();
    // $row["prescriptionid"] = $prescriptionid;
    // $row["medicineproductid"] = $medicineproductid;
    // $row["medicine_title"] = $medicine_title;
    // $row["size_pack"] = $size_pack;
    // $row["pack_unit"] = $pack_unit;
    // $row["drug_way"] = $drug_way;
    // $row["drug_dose"] = $drug_dose;
    // $row["drug_frequency"] = $drug_frequency;
    // $row["cnt"] = $cnt;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PrescriptionItem::createByBiz row cannot empty");

        $default = array();
        $default["prescriptionid"] = 0;
        $default["medicineproductid"] = 0;
        $default["medicine_title"] = '';
        $default["size_pack"] = '';
        $default["pack_unit"] = '';
        $default["drug_way"] = '';
        $default["drug_dose"] = '';
        $default["drug_frequency"] = '';
        $default["cnt"] = 0;
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
