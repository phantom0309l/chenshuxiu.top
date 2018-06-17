<?php

/*
 * SimpleSheet
 */
class SimpleSheet extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid'  // wxuserid
            ,'userid'  // userid
            ,'patientid'    //patientid
            ,'simplesheettplid'    //simplesheettplid
            ,'thedate'    //填写日期
            ,'content'    //答案,key-value形式
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid' ,'simplesheettplid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser","key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User","key" => "userid");
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["simplesheettpl"] = array ("type" => "SimpleSheetTpl", "key" => "simplesheettplid" );
    }

    // $row = array(); 
    // $row["patientid"] = $patientid;
    // $row["simplesheettplid"] = $simplesheettplid;
    // $row["thedate"] = $thedate;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "SimpleSheet::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["simplesheettplid"] =  0;
        $default["thedate"] =  '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTitleStr () {
        if ($this->simplesheettpl->ename == 'PH_daily') {
            return "添加日常记录";
        }
    }
}
