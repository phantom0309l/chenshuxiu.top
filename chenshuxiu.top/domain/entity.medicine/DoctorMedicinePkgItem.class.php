<?php
// DoctorMedicinePkgItem
// 医生-基本药方-用药条目

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class DoctorMedicinePkgItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctormedicinepkgid',  // doctormedicinepkgid
            'medicineid',  // medicineid
            'drug_std_dosage',  // 标准用法,75mg/m2
            'drug_dose',  // 药物剂量
            'drug_frequency',  // 用药频率
            'drug_change',  // 用药规则
            'herbjson',  // 用药规则
            'doctorremark'); // 医生填写的注意事项
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctormedicinepkgid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctormedicinepkg"] = array(
            "type" => "DoctorMedicinePkg",
            "key" => "doctormedicinepkgid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["doctormedicinepkgid"] = $doctormedicinepkgid;
    // $row["medicineid"] = $medicineid;
    // $row["drug_std_dosage"] = $drug_std_dosage;
    // $row["drug_dose"] = $drug_dose;
    // $row["drug_frequency"] = $drug_frequency;
    // $row["drug_change"] = $drug_change;
    // $row["doctorremark"] = $doctorremark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorMedicinePkgItem::createByBiz row cannot empty");

        $default = array();
        $default["doctormedicinepkgid"] = 0;
        $default["medicineid"] = 0;
        $default["drug_std_dosage"] = '';
        $default["drug_dose"] = '';
        $default["drug_frequency"] = '';
        $default["drug_change"] = '';
        $default["herbjson"] = '';
        $default["doctorremark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    private $_doctorMedicineRef = null;

    // 获取 DoctorMedicineRef
    public function getDoctorMedicineRef () {
        if (false == $this->_doctorMedicineRef instanceof DoctorMedicineRef) {
            $this->_doctorMedicineRef = DoctorMedicineRefDao::getByDoctoridMedicineid($this->doctormedicinepkg->doctorid, $this->medicineid);
        }

        return $this->_doctorMedicineRef;
    }

    // 封装, 给药途径 数组
    public function getDrug_way_arr () {
        return $this->medicine->drug_way_arr;
    }

    // 封装, 标准用法 数组
    public function getDrug_std_dosage_arr () {
        return $this->getDoctorMedicineRef()->drug_std_dosage_arr;
    }

    // 封装, 用药时机 数组
    public function getDrug_timespan_arr () {
        return $this->getDoctorMedicineRef()->drug_timespan_arr;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
