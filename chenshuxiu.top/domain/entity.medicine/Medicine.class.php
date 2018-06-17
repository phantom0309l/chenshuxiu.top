<?php

/*
 * Medicine
 */
class Medicine extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            "pinyin",
            'name',  // 通俗用名
            'scientificname',  // 学名
            'groupstr',  // 分组
            "unit",
            'pictureid',
            'content',  // 描述
            'isshow',
            'drug_way_arr',  // 给药途径,口服|输液等
            'drug_std_dosage_arr',  // 标准用法,75mg/m2
            'drug_timespan_arr',  // 用药时机(第一天,第三天-第5天 等)
            'drug_dose_arr',  // 药物剂量,备选项,逗号分隔
            'drug_frequency_arr',  // 用药频率,备选项,逗号分隔
            'drug_change_arr',  // 调药规则,备选项,逗号分隔
            'ischinese',  // 是否是中药
            'herbjson',  // 成分
            'doctorremark',  // 注意事项
            "remark");
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // 非患者端提交, 并发比较少, 采用自增id
    public static function createByBiz ($row) {
        if (empty($row['id'])) {
            $row['id'] = self::getMaxId() + 1;
        }

        return self::createByBizImp($row);
    }

    // 患者端提交, 考虑并发, 用id生成器
    public static function createByPatient ($row) {
        return self::createByBizImp($row);
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["scientificname"] = $scientificname;
    // $row["content"] = $content;
    public static function createByBizImp ($row) {
        DBC::requireNotEmpty($row, "Medicine::createByBiz row cannot empty");
        $default = array();
        $default["pinyin"] = PinyinUtil::Pinyin($row["name"]);
        $default["name"] = '';
        $default["scientificname"] = '';
        $default["groupstr"] = '';
        $default["unit"] = '';
        $default['pictureid'] = 0;
        $default["content"] = '';
        $default['isshow'] = 0;
        $default['drug_way_arr'] = '';
        $default['drug_std_dosage_arr'] = '';
        $default['drug_timespan_arr'] = '';
        $default["drug_dose_arr"] = '';
        $default["drug_frequency_arr"] = '';
        $default["drug_change_arr"] = '';
        $default["ischinese"] = 0;
        $default["herbjson"] = '';
        $default["doctorremark"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public function getVvArray ($filedName) {
        $arr = self::getShowArr($this->$filedName);
        return self::kvArrayToVvArray($arr);
    }

    // ArrDrug_way
    public function getArrDrug_way () {
        return self::getShowArr($this->drug_way_arr);
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

    // Drug_way
    public function getDefaultDrug_way () {
        $arr = self::getExplodeResult($this->drug_way_arr);
        return self::getDefaultResult($arr);
    }

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
    // ------------ obj method ------------
    // ====================================
    public function getDiseaseMedicineRefs () {
        return DiseaseMedicineRefDao::getListByMedicine($this);
    }

    public function getSubContent () {
        $str = mb_substr($this->content, 0, 10);
        if (false == empty($str)) {
            $str = " ...";
        }

        return $str;
    }

    public function isShowOnWww () {
        return $this->isshow == XConst::bool_yes;
    }

    public function getDoctorMedicineRefs () {
        return DoctorMedicineRefDao::getListByMedicineid($this->id);
    }

    public function getDoctorMedicineRefCnt () {
        return DoctorMedicineRefDao::getCntByMedicineid($this->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    private static function getMaxId () {
        return Dao::queryValue("select max(id) from medicines where id < 10000000");
    }

    public static function get_drug_frequency_str ($drug_frequency) {
        $arr = self::get_drug_frequency_Arr_define();

        return $arr[$drug_frequency];
    }

    public static function get_drug_frequency_Arr_define () {
        $arr = array();
        $arr['qd'] = '每天1次';
        $arr['bid'] = '每天2次';
        $arr['tid'] = '每天3次';
        $arr['qod'] = '隔天1次';
        $arr['qw'] = '每周1次';
        $arr['qow'] = '隔周1次';
        return $arr;
    }

    public static function getGroupstrArr ($isall = 1) {
        $sql = " select groupstr from medicines where groupstr<>'' group by groupstr";

        $groups = Dao::queryValues($sql, []);

        if ($isall) {
            $groups[] = '';
        }
        return $groups;
    }

    // kvArrayToVvArray
    public static function kvArrayToVvArray ($arr) {
        $arrFix = array();
        foreach ($arr as $v) {
            $arrFix[$v] = $v;
        }
        return $arrFix;
    }

    // 切分后的备选项的数组, 并去掉 *
    public static function getShowArr ($str) {
        $str = str_replace("*", "", $str);
        $arr = self::getExplodeResult($str);
        return $arr;
    }

    // 切分后的备选项的数组
    public static function getExplodeResult ($str) {
        $arr = explode("|", $str);
        $arrFix = array();
        foreach ($arr as $a) {
            $a = trim($a);
            if (empty($a)) {
                continue;
            }

            $arrFix[] = $a;
        }

        return $arrFix;
    }

    // 默认选中项的值
    public static function getDefaultResult ($arr) {
        foreach ($arr as $value) {
            if (strpos($value, '*') === 0) {
                return mb_substr($value, 1);
            }
        }

        if (empty($arr)) {
            return '';
        }
        return $arr[0];
    }

    // ---- static resourse ----
    //ADHD 运营关注的患者用药
    public static $masterMedicines = [2, 396, 3, 45, 185, 21, 10, 41, 9, 182, 122];
}
