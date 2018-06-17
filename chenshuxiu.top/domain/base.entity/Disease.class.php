<?php

/*
 * Disease
 */
class Disease extends Entity
{
    public static function get301Disease () {
        return Disease::getById(25);
    }

    public static function getCancerDiseaseidsStr() {
        return implode(',', self::getCancerDiseaseidArray());
    }

    public static function getCancerDiseaseidArray () {
        return [8, 14, 15, 19, 21];
    }

    public static function getMultDiseaseIdArray () {
        return [2, 3, 6, 22];
    }

    public static function isMultDisease($diseaseid) {
        return in_array($diseaseid, Disease::getMultDiseaseIdArray());
    }

    public static function isCancer ($diseaseid) {
        return in_array($diseaseid, Disease::getCancerDiseaseidArray());
    }

    public static function isNMO ($diseaseid) {
        return $diseaseid == 3;
    }

    public static function isADHD ($diseaseid) {
        return $diseaseid == 1;
    }

    public static function isASD ($diseaseid) {
        return $diseaseid == 24;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseasegroupid',  // 疾病分组id
            'name',  // 疾病名
            'code'); // code
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
    // $row["diseasegroupid"] = $diseasegroupid;
    // $row["name"] = $name;
    // $row["diseasegroupid"] = $diseasegroupid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Disease::createByBiz row cannot empty");

        $default = array();
        $default["diseasegroupid"] = 1;
        $default["name"] = '';
        $default["code"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 当前疾病关联的服务号
    public function getWxShop () {
        return WxShopDao::getByDiseaseid($this->id);
    }

    // 当前疾病关联医生列表
    public function getDoctors () {
        return DoctorDao::getListByDiseaseid($this->id);
    }

    // 疾病药品关联列表
    public function getDiseaseMedicineRefs () {
        return DiseaseMedicineRefDao::getListByDisease($this);
    }

    public function getDiseasePaperTplRefs ($show_in_audit = null) {
        return DiseasePaperTplRefDao::getListByDiseaseidDoctorid($this->id, 0, $show_in_audit);
    }

    public function getPaperTpls ($show_in_audit = null) {
        return array_map(function  ($x) {
            return $x->papertpl;
        }, $this->getDiseasePaperTplRefs($show_in_audit));
    }

    //是不是多动症疾病组
    public function isInDiseaseGroupOfADHD(){
        return 2 == $this->diseasegroupid;
    }

    // 没绑定的医生
    public function getUnbindDoctors () {
        $arr = array();
        foreach (DiseaseDao::getListAll() as $m) {
            if (false == $this->getDoctors($m)) {
                $arr[] = $m;
            }
        }
        return $arr;
    }

    // 没绑定的药品
    public function getUnbindMedicines () {
        $arr = array();
        foreach (MedicineDao::getListAll() as $m) {
            if (false == $this->isBindMedicine($m)) {
                $arr[] = $m;
            }
        }

        return $arr;
    }

    // 没绑定的量表
    public function getUnbindPaperTpls () {
        $arr = array();
        foreach (PaperTplDao::getAllList() as $p) {
            if (false == $this->isBindPaperTpl($p)) {
                $arr[] = $p;
            }
        }

        return $arr;
    }

    // 疾病药品已关联
    public function isBindMedicine (Medicine $medicine) {
        $ref = DiseaseMedicineRefDao::getByDiseaseAndMedicine($this, $medicine);
        return ($ref instanceof DiseaseMedicineRef);
    }

    // 疾病量表已关联
    public function isBindPaperTpl (PaperTpl $papertpl) {
        $ref = DiseasePaperTplRefDao::getByDiseaseAndPaperTpl($this, $papertpl);
        return ($ref instanceof DiseasePaperTplRef);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 获取多疾病id
    public static function getMultDiseaseIds () {
        $mult_diseaseids = [
            'ILD' => 2,
            'NMO' => 3,
            'MPN' => 6,
            'PH' => 22
        ];

        return $mult_diseaseids;
    }

    // 获取肿瘤疾病Array
    public static function getCancerDiseaseArrForBaodao () {
        $arr = [
            '未选择' => 0,
            '肺癌' => 8,
            '胃癌' => 15,
            '结直肠癌' => 21,
            '胰腺癌' => 19,
            '肝癌' => 19,
            '胆囊癌' => 19,
            '胆管癌' => 19,
            '甲状腺癌' => 19,
            '食管癌' => 19,
            '脑肿瘤' => 19,
            '淋巴瘤' => 19,
            '多发性骨髓瘤' => 19,
            '黑色素瘤' => 19,
            '鼻咽癌' => 19,
            '乳腺癌' => 19,
            '胸腺癌' => 19,
            '纵膈肿瘤' => 19,
            '胃肠道间质瘤' => 19,
            '其他肿瘤' => 19
        ];

        return $arr;
    }

    /*
    5404: 气道狭隘患者发送【呼吸困难量表】
    第0周（0<X<7）：第1天，晚上7点
    第2周（14≤X<21）：第15天，晚上7点
    第4周（28≤X<35）：第29天，晚上7点
    第2月（60≤X<90）：第62天，晚上7点
    第3月（90≤X<120）：第92天，晚上7点
    第6月（180≤X<210）：第182天，晚上7点
    第1年（365≤X<730）：第367天，晚上7点
    */
    public static function getDaysQDXZ () {
        $days = [1, 15, 29, 62, 92, 182, 367];

        return $days;
    }
}
