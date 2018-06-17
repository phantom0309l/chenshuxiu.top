<?php
/*
 * Linkman
 */
class Linkman extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'name'    //name
        ,'shipstr'    //关系
        ,'mobile'    //号码
        ,'is_master'    //是否为主联系人 1:是 0:否
        ,'xprovinceid'    //电话号码所在省id
        ,'xcityid'    //电话号码所在市id
        ,'fetch_cnt'    //抓取次数
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["xprovince"] = array ("type" => "Xprovince", "key" => "xprovinceid" );
        $this->_belongtos["xcity"] = array ("type" => "Xcity", "key" => "xcityid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["name"] = $name;
    // $row["shipstr"] = $shipstr;
    // $row["mobile"] = $mobile;
    // $row["is_master"] = $is_master;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"Linkman::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["name"] = '';
        $default["shipstr"] = '';
        $default["mobile"] = '';
        $default["is_master"] =  0;
        $default["xprovinceid"] =  0;
        $default["xcityid"] =  0;
        $default["fetch_cnt"] =  0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getMarkMobile () {
        return substr($this->mobile, 0, 3) . "****" . substr($this->mobile, - 4);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getShipstrs () {
        $shipstrs = [
            '本人',
            '配偶',
            '父子',
            '父女',
            '母子',
            '母女',
            '祖孙',
            '朋友',
            '子女',
            '兄弟姐妹',
            '其他'
        ];

        return $shipstrs;
    }

}
