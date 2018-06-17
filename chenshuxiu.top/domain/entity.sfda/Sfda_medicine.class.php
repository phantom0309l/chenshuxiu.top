<?php

/*
 * Sfda_medicine
 */
class Sfda_medicine extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'sfda_id',  // 药监局药品id
            'pizhun_date',  // 批准日期
            'end_date',  // 截止日期
            'piwenhao',  // 批准文号
            'piwenhao_old',  // 原批准文号
            'name_common',  // 产品名称
            'name_common_en',  // 产品名称,英文
            'name_brand',  // 商品名称,品牌名,可空
            'name_brand_en',  // 商品名称,品牌名,英文,可空
            'company_name',  // 生产单位
            'company_name_en',  // 生产单位, 英文
            'type_jixing',  // 剂型
            'type_chanpin',  // 产品类别
            'size_chengfen',  // 单位规格
            'size_pack',  // 包装规格
            'benweima',  // 药品本位码
            'benweima_remark',  // 药品本位码备注
            'remark',
            'en_json',  // 进口信息
            'is_en', // 是否进口
        ); //
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'sfda_id');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["sfda_id"] = $sfda_id;
    // $row["pizhun_date"] = $pizhun_date;
    // $row["end_date"] = $end_date;
    // $row["piwenhao"] = $piwenhao;
    // $row["piwenhao_old"] = $piwenhao_old;
    // $row["name_common"] = $name_common;
    // $row["name_common_en"] = $name_common_en;
    // $row["name_brand"] = $name_brand;
    // $row["name_brand_en"] = $name_brand_en;
    // $row["company_name"] = $company_name;
    // $row["company_name_en"] = $company_name_en;
    // $row["type_jixing"] = $type_jixing;
    // $row["type_chanpin"] = $type_chanpin;
    // $row["size_chengfen"] = $size_chengfen;
    // $row["size_pack"] = $size_pack;
    // $row["benweima"] = $benweima;
    // $row["benweima_remark"] = $benweima_remark;
    // $row["remark"] = $remark;
    // $row["en_json"] = $en_json;
    // $row["is_en"] = $is_en;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Sfda_medicine::createByBiz row cannot empty");

        $default = array();
        $default["sfda_id"] = '';
        $default["pizhun_date"] = '';
        $default["end_date"] = '';
        $default["piwenhao"] = '';
        $default["piwenhao_old"] = '';
        $default["name_common"] = '';
        $default["name_common_en"] = '';
        $default["name_brand"] = '';
        $default["name_brand_en"] = '';
        $default["company_name"] = '';
        $default["company_name_en"] = '';
        $default["type_jixing"] = '';
        $default["type_chanpin"] = '';
        $default["size_chengfen"] = '';
        $default["size_pack"] = '';
        $default["benweima"] = '';
        $default["benweima_remark"] = '';
        $default["remark"] = '';
        $default["en_json"] = '';
        $default["is_en"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function type_chanpinJsonArray () {
        $arr = [];
        $arr['all'] = '全部';
        $arr['中药'] = '中药';
        $arr['化学药品'] = '化学药品';
        $arr['生物制品'] = '生物制品';
        $arr['辅料'] = '辅料';

        return $arr;
    }
}
