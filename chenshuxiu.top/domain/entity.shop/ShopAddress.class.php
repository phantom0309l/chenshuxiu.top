<?php

/*
 * ShopAddress
 */
class ShopAddress extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'linkman_name',  // 联系人姓名
            'linkman_mobile',  // 联系人姓名
            'xprovinceid',  // 省id
            'xcityid',  // 市id
            'xcountyid',  // 区id
            'content',  // 具体地址
            'postcode',  // 邮编
            'is_master'); // 主配送地址
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");

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
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["linkman_name"] = $linkman_name;
    // $row["linkman_mobile"] = $linkman_mobile;
    // $row["xprovinceid"] = $provinceid;
    // $row["xcityid"] = $cityid;
    // $row["xcountyid"] = $quid;
    // $row["content"] = $content;
    // $row["postcode"] = $postcode;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ShopAddress::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["linkman_name"] = '';
        $default["linkman_mobile"] = '';
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = '';
        $default["postcode"] = '';
        $default["is_master"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    //获取详细地址
    public function getDetailAddress(){
        $four = [110000, 120000, 310000, 500000];
        if (in_array($this->xprovinceid, $four)) {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = "";
        } else {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = $this->xcity->name;
        }
        $xcounty_name = $this->xcounty->name;
        $content = $this->content;

        return "{$xprovince_name}{$xcity_name}{$xcounty_name}{$content}";
    }

}
