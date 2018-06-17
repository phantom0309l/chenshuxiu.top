<?php

/*
 * DiseasemedicineRef
 */
class DiseaseMedicineRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseaseid',  // 疾病id
            'medicineid',  // 药品id
            'pos',  // 排序
            'drug_std_dosage_arr',  // 标准用法,75mg/m2
            'drug_timespan_arr',  // 用药时机(第一天,第三天-第5天 等)
            'drug_dose_arr',  // 药物剂量,备选项,逗号分隔
            'drug_frequency_arr',  // 用药频率,备选项,逗号分隔
            'drug_change_arr',  // 调药规则,备选项,逗号分隔
            'herbjson',  // 成分
            'doctorremark',  // 注意事项
            'level',  // 等级 =9 运营关注的药
            'normal_doses',  // 常用剂量,暂时没用
            'max_dose'); // 最大剂量
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'diseaseid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["diseaseid"] = $diseaseid;
    // $row["medicineid"] = $medicineid;
    // $row["pos"] = $pos;
    // $row["level"] = $level;
    // $row["normal_doses"] = $normal_doses;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DiseaseMedicineRef::createByBiz row cannot empty");

        $default = array();
        $default["diseaseid"] = 0;
        $default["medicineid"] = 0;
        $default["pos"] = 0;
        $default['drug_std_dosage_arr'] = '';
        $default['drug_timespan_arr'] = '';
        $default["drug_dose_arr"] = '';
        $default["drug_frequency_arr"] = '';
        $default["drug_change_arr"] = '';
        $default["herbjson"] = '';
        $default["doctorremark"] = '';
        $default["level"] = 0;
        $default["normal_doses"] = '';
        $default["max_dose"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
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

    public static function getGroupstrArr ($diseaseid = 0, $isall = 1) {
        $cond = '';
        $bind = [];

        if ($diseaseid) {
            $cond = " and dmf.diseaseid = :diseaseid";
            $bind[':diseaseid'] = $diseaseid;
        }

        $sql = " select m.groupstr
            from diseasemedicinerefs dmf
            inner join medicines m on m.id=dmf.medicineid
            where m.groupstr<>'' {$cond}
            group by m.groupstr";

        $groups = Dao::queryValues($sql, $bind);

        if ($isall) {
            $groups[] = '';
        }
        return $groups;
    }
}
