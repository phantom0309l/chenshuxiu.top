<?php

/*
 * PatientTodayMark
 */
class PatientTodayMark extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'doctorid'    //doctorid
        ,'patienttodaymarktplid'    //备注选项
        ,'thedate'    //日期
        ,'title'    //备注内容
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid' ,'userid' ,'patientid' ,'doctorid' ,'patienttodaymarktplid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        
    $this->_belongtos["wxuser"] = array ("type" => "Wxuser", "key" => "wxuserid" );
    $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
    $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
    $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    $this->_belongtos["patienttodaymarktpl"] = array ("type" => "Patienttodaymarktpl", "key" => "patienttodaymarktplid" );
    }

    // $row = array(); 
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["patienttodaymarktplid"] = $patienttodaymarktplid;
    // $row["thedate"] = $thedate;
    // $row["title"] = $title;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientTodayMark::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["doctorid"] =  0;
        $default["patienttodaymarktplid"] =  0;
        $default["thedate"] = '';
        $default["title"] = '';
        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public static function getTodayMarkTpl(){
        return PatientTodayMarkTplDao::getEntityListByCond('PatientTodayMarkTpl');
    }
}
