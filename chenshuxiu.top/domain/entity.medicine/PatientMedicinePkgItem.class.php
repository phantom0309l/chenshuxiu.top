<?php
// PatientMedicinePkgItem
// 药方条目

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class PatientMedicinePkgItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'patientmedicinepkgid',  // patientmedicinepkgid
            'medicineid',  // medicineid
            'drug_dose',  // 药物剂量
            'drug_frequency',  // 用药频率
            'drug_change',  // 调药规则
            'herbjson',  // 成分
            'doctorremark'); // 医生填写的注意事项
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientmedicinepkgid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patientmedicinepkg"] = array(
            "type" => "PatientMedicinePkg",
            "key" => "patientmedicinepkgid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["patientmedicinepkgid"] = $patientmedicinepkgid;
    // $row["medicineid"] = $medicineid;
    // $row["drug_dose"] = $drug_dose;
    // $row["drug_frequency"] = $drug_frequency;
    // $row["drug_change"] = $drug_change;
    // $row["doctorremark"] = $doctorremark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientMedicinePkgItem::createByBiz row cannot empty");

        $default = array();
        $default["patientmedicinepkgid"] = 0;
        $default["medicineid"] = 0;
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

    // Drug_frequencyStr
    public function getDrug_frequencyStr () {
        $str = Medicine::get_drug_frequency_str($this->drug_frequency);

        if (empty($str)) {
            $str = $this->drug_frequency;
        }

        return $str;
    }

    // getDoctorMedicineRef
    public function getDoctorMedicineRef () {

        // done pcard fix , patientmedicinepkg->doctorid 值有问题,需要修复
        $revisitrecord = $this->patientmedicinepkg->revisitrecord;

        return DoctorMedicineRefDao::getByDoctoridMedicineid($revisitrecord->doctorid, $this->medicineid);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
