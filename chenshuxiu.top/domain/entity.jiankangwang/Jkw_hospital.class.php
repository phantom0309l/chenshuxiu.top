<?php
/*
 * Jkw_hospital
 */
class Jkw_hospital extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'name'    //医院名称
        ,'shortname'    //短名称
        ,'logo_pictureid'    //医院logo图片
        ,'type'    //医院性质
        ,'levelstr'    //等级
        ,'mobile'    //电话
        ,'xprovinceid'  // 省id
        ,'xcityid'  // 市id
        ,'xcountyid'  // 区id
        ,'content'  // 详细地址
        ,'picture_url'    //医院图片链接
        ,'president_name'    //院长姓名
        ,'found_year'    //建院年份
        ,'department_cnt'    //科室数量
        ,'employee_cnt'    //医护人数
        ,'bed_cnt'    //病床数量
        ,'is_yibao'    //是否医保 默认是0  医保是1  非医保是2
        ,'website'    //网址
        ,'postalcode'    //邮政编码
        ,'brief'    //简介
        ,'bus_route'    //公交路线
        ,'from_url'    //抓取信息来源的网址
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["logo_picture"] = array ("type" => "Picture", "key" => "logo_pictureid" );
        $this->_belongtos["xprovince"] = array(
            "type" => "Xprovince",
            "key" => "xprovinceid");
        $this->_belongtos["xcity"] = array(
            "type" => "Xcity",
            "key" => "xcityid");
        $this->_belongtos["xcounty"] = array(
            "type" => "Xcounty",
            "key" => "xcountyid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["shortname"] = $shortname;
    // $row["logo_pictureid"] = $logo_pictureid;
    // $row["type"] = $type;
    // $row["levelstr"] = $levelstr;
    // $row["mobile"] = $mobile;
    // $row["xprovinceid"] = 0;
    // $row["xcityid"] = 0;
    // $row["xcountyid"] = 0;
    // $row["content"] = '';
    // $row["picture_url"] = $picture_url;
    // $row["president_name"] = $president_name;
    // $row["found_year"] = $found_year;
    // $row["department_cnt"] = $department_cnt;
    // $row["employee_cnt"] = $employee_cnt;
    // $row["bed_cnt"] = $bed_cnt;
    // $row["is_yibao"] = $is_yibao;
    // $row["website"] = $website;
    // $row["postalcode"] = $postalcode;
    // $row["brief"] = $brief;
    // $row["bus_route"] = $bus_route;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"Jkw_hospital::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["shortname"] = '';
        $default["logo_pictureid"] =  0;
        $default["type"] = '';
        $default["levelstr"] = '';
        $default["mobile"] = '';
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = '';
        $default["picture_url"] =  '';
        $default["president_name"] = '';
        $default["found_year"] = '';
        $default["department_cnt"] =  0;
        $default["employee_cnt"] =  0;
        $default["bed_cnt"] =  0;
        $default["is_yibao"] =  0;
        $default["website"] = '';
        $default["postalcode"] =  0;
        $default["brief"] = '';
        $default["bus_route"] = '';
        $default["from_url"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getJkw_hospitalAddressStr () {
        return $this->xprovince->name . $this->xcity->name . $this->xcounty->name . $this->content;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
