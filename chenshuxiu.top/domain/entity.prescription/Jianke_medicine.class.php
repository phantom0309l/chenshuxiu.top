<?php

/*
 * Jianke_medicine
 */
class Jianke_medicine extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'sfda_medicineid',  // sfda_medicineid
            'pictureid',  // pictureid
            'piwenhao',  // 批准文号
            'name_common',  // 通用名, 法定名称
            'name_common_en',  // 通用名, 英文
            'name_brand',  // 品牌名/商品名 可空
            'name_brand_en',  // 品牌名/商品名,英文 可空
            'company_name',  // 生产单位
            'company_name_en',  // 生产单位,英文
            'yuanliao',  // 主要原料
            'zuoyong',  // 主要作用
            'yongfa',  // 用法用量
            'size_chanpin',  // 产品规格:单位规格+包装规格
            'content',  // 说明书
            'picture_url'); // 图片原url
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["sfda_medicine"] = array(
            "type" => "Sfda_medicine",
            "key" => "sfda_medicineid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["sfda_medicineid"] = $sfda_medicineid;
    // $row["pictureid"] = $pictureid;
    // $row["piwenhao"] = $piwenhao;
    // $row["name_common"] = $name_common;
    // $row["name_common_en"] = $name_common_en;
    // $row["name_brand"] = $name_brand;
    // $row["name_brand_en"] = $name_brand_en;
    // $row["company_name"] = $company_name;
    // $row["company_name_en"] = $company_name_en;
    // $row["yuanliao"] = $yuanliao;
    // $row["zuoyong"] = $zuoyong;
    // $row["yongfa"] = $yongfa;
    // $row["size_chanpin"] = $size_chanpin;
    // $row["content"] = $content;
    // $row["picture_url"] = $picture_url;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Jianke_medicine::createByBiz row cannot empty");

        $default = array();
        $default["sfda_medicineid"] = 0;
        $default["pictureid"] = 0;
        $default["piwenhao"] = '';
        $default["name_common"] = '';
        $default["name_common_en"] = '';
        $default["name_brand"] = '';
        $default["name_brand_en"] = '';
        $default["company_name"] = '';
        $default["company_name_en"] = '';
        $default["yuanliao"] = '';
        $default["zuoyong"] = '';
        $default["yongfa"] = '';
        $default["size_chanpin"] = '';
        $default["content"] = '';
        $default["picture_url"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
