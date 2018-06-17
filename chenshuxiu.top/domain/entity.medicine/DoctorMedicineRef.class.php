<?php
// DoctorMedicineRef
// 医生-药品-关联表

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class DoctorMedicineRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  //
            'medicineid',  //
            'title',  // 药物给医生展示名
            'drug_std_dosage_arr',  // 标准用法,75mg/m2
            'drug_timespan_arr',  // 用药时机(第一天,第三天-第5天 等)
            'drug_dose_arr',  // 药物剂量,备选项,逗号分隔
            'drug_frequency_arr',  // 用药频率,备选项,逗号分隔
            'drug_change_arr',  // 调药规则,备选项,逗号分隔
            'herbjson',  // 调药规则,备选项,逗号分隔
            'doctorremark',  // 医生填写的注意事项
            'pos'); // pos
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["medicineid"] = $medicineid;
    // $row["title"] = $title;
    // $row["drug_dose_arr"] = $drug_dose_arr;
    // $row["drug_frequency_arr"] = $drug_frequency_arr;
    // $row["drug_change_arr"] = $drug_change_arr;
    // $row["doctorremark"] = $doctorremark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorMedicineRef::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["medicineid"] = 0;
        $default["title"] = '';
        $default['drug_std_dosage_arr'] = '';
        $default['drug_timespan_arr'] = '';
        $default["drug_dose_arr"] = '';
        $default["drug_frequency_arr"] = '';
        $default["drug_change_arr"] = '';
        $default["herbjson"] = '';
        $default["doctorremark"] = '';
        $default["pos"] = 9999;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // array[$v] = $v;
    // 为啥转换?
    public function getVvArray ($filedName) {
        $arr = self::getShowArr($this->$filedName);
        return self::kvArrayToVvArray($arr);
    }

    // ArrDrug_std_dosage
    public function getArrDrug_std_dosage () {
        return self::getShowArr($this->drug_std_dosage_arr);
    }

    // ArrDrug_timespan
    public function getArrDrug_timespan () {
        return self::getShowArr($this->drug_timespan_arr);
    }

    // ArrDrug_dose
    public function getArrDrug_dose () {
        return self::getShowArr($this->drug_dose_arr);
    }

    // ArrDrug_frequency
    public function getArrDrug_frequency () {
        return self::getShowArr($this->drug_frequency_arr);
    }

    // ArrDrug_change
    public function getArrDrug_change () {
        return self::getShowArr($this->drug_change_arr);
    }

    // 默认值

    // Drug_std_dosage
    public function getDefaultDrug_std_dosage () {
        $arr = self::getExplodeResult($this->drug_std_dosage_arr);
        return self::getDefaultResult($arr);
    }

    // Drug_timespan
    public function getDefaultDrug_timespan () {
        $arr = self::getExplodeResult($this->drug_timespan_arr);
        return self::getDefaultResult($arr);
    }

    // Drug_dose
    public function getDefaultDrug_dose () {
        $arr = self::getExplodeResult($this->drug_dose_arr);
        return self::getDefaultResult($arr);
    }

    // Drug_frequency
    public function getDefaultDrug_frequency () {
        $arr = self::getExplodeResult($this->drug_frequency_arr);
        return self::getDefaultResult($arr);
    }

    // Drug_change
    public function getDefaultDrug_change () {
        $arr = self::getExplodeResult($this->drug_change_arr);
        return self::getDefaultResult($arr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // kvArrayToVvArray
    public static function kvArrayToVvArray ($arr) {
        return Medicine::kvArrayToVvArray($arr);
    }

    // 切分后的备选项的数组, 并去掉 *
    public static function getShowArr ($str) {
        return Medicine::getShowArr($str);
    }

    // 切分后的备选项的数组
    public static function getExplodeResult ($str) {
        return Medicine::getExplodeResult($str);
    }

    // 默认选中项的值
    public static function getDefaultResult ($arr) {
        return Medicine::getDefaultResult($arr);
    }
}
