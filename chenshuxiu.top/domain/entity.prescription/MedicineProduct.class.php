<?php

/*
 * MedicineProduct 新药品表
 */
class MedicineProduct extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'sfda_medicineid',  // sfda_medicineid
            'medicineid',  // medicineid
            'pictureid',  // pictureid
            'name_common',  // 通用名, 法定名称
            'name_common_en',  // 通用名, 英文
            'name_brand',  // 品牌名/商品名 可空
            'name_brand_en',  // 品牌名/商品名,英文 可空
            'name_chem',  // 学名/化学名 可空
            'name_chem_en',  // 学名/化学名,英文 可空
            'drug_way',  // 给药途径,口服|输液等
            'drug_dose',  // 单次用药剂量
            'drug_frequency',  // 用药频率
            'pack_unit',  // 包装单位
            'yuanliao',  // 主要原料
            'zuoyong',  // 主要作用
            'yongfa',  // 用法用量
            'content',  // 说明书
            'type_jixing',  // 剂型
            'type_chanpin',  // 产品类别
            'size_chengfen',  // 单位规格
            'size_pack',  // 包装规格
            'pizhun_date',  // 批准日期
            'piwenhao',  // 批准文号
            'benweima',  // 药品本位码
            'company_name',  // 生产单位
            'company_name_en',  // 生产单位,英文
            'status',  // 状态
            'remark',  // remark
            'is_tongbu_chufang_system'); // 已经同步处方系统
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'sfda_medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["sfda_medicine"] = array(
            "type" => "Sfda_medicine",
            "key" => "sfda_medicineid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["sfda_medicineid"] = $sfda_medicineid;
    // $row["pictureid"] = $pictureid;
    // $row["name_common"] = $name_common;
    // $row["name_common_en"] = $name_common_en;
    // $row["name_brand"] = $name_brand;
    // $row["name_brand_en"] = $name_brand_en;
    // $row["name_chem"] = $name_chem;
    // $row["name_chem_en"] = $name_chem_en;
    // $row["drug_way"] = $drug_way;
    // $row["drug_dose"] = $drug_dose;
    // $row["drug_frequency"] = $drug_frequency;
    // $row["pack_unit"] = $pack_unit;
    // $row["yuanliao"] = $yuanliao;
    // $row["zuoyong"] = $zuoyong;
    // $row["yongfa"] = $yongfa;
    // $row["content"] = $content;
    // $row["type_jixing"] = $type_jixing;
    // $row["type_chanpin"] = $type_chanpin;
    // $row["size_chengfen"] = $size_chengfen;
    // $row["size_pack"] = $size_pack;
    // $row["pizhun_date"] = $pizhun_date;
    // $row["piwenhao"] = $piwenhao;
    // $row["benweima"] = $benweima;
    // $row["company_name"] = $company_name;
    // $row["company_name_en"] = $company_name_en;
    // $row["status"] = $status;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "MedicineProduct::createByBiz row cannot empty");

        $default = array();
        $default["sfda_medicineid"] = 0;
        $default["medicineid"] = 0;
        $default["pictureid"] = 0;
        $default["name_common"] = '';
        $default["name_common_en"] = '';
        $default["name_brand"] = '';
        $default["name_brand_en"] = '';
        $default["name_chem"] = '';
        $default["name_chem_en"] = '';
        $default["drug_way"] = '';
        $default["drug_dose"] = '';
        $default["drug_frequency"] = '';
        $default["pack_unit"] = '';
        $default["yuanliao"] = '';
        $default["zuoyong"] = '';
        $default["yongfa"] = '';
        $default["content"] = '';
        $default["type_jixing"] = '';
        $default["type_chanpin"] = '';
        $default["size_chengfen"] = '';
        $default["size_pack"] = '';
        $default["pizhun_date"] = '';
        $default["piwenhao"] = '';
        $default["benweima"] = '';
        $default["company_name"] = '';
        $default["company_name_en"] = '';
        $default["status"] = 0;
        $default["remark"] = '';
        $default["is_tongbu_chufang_system"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 拼一个title
    public function getTitle () {
        $str = $this->name_common;
        $name_brand = $this->name_brand;
        if ($name_brand && false === strpos($str, $name_brand)) {
            $str .= "({$this->name_brand})";
        }

        return $str;
    }

    // 已入驻商城
    public function isInShop () {
        return ShopProductDao::getShopProductByObjtypeObjid('MedicineProduct', $this->id) instanceof ShopProduct;
    }

    // title 用于生成 ShopProduct
    public function getTitleForShopProduct () {
        return $this->getTitle() . ",{$this->size_pack},{$this->size_chengfen}";
    }

    // content 用于生成 ShopProduct
    public function getContentForShopProduct () {
        $str = "";
        $str .= "\n药品名称: {$this->getTitle()}";
        $str .= "\n主要原料: {$this->yuanliao}";
        $str .= "\n主要作用: {$this->zuoyong}";
        $str .= "\n产品规格: {$this->size_pack} ; {$this->size_chengfen}";
        $str .= "\n用法用量: {$this->yongfa}";
        $str .= "\n批准文号: {$this->piwenhao}";
        $str .= "\n生产企业: {$this->company_name}";

        $str = trim($str);

        return $str;
    }
}
