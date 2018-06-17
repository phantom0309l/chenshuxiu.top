<?php

/*
 * Certican
 */
class Certican extends Entity
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
            ,'title'    //化疗方案
            ,'sub_title'    //程数
            ,'begin_date'    //开始日期
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid' ,'userid' ,'patientid' ,'doctorid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["title"] = $title;
    // $row["sub_title"] = $sub_title;
    // $row["begin_date"] = $begin_date;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Certican::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["doctorid"] =  0;
        $default["title"] = '';
        $default["sub_title"] = '';
        $default["begin_date"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getItemCnt () {
        $items = CerticanItemDao::getListByCertican($this);

        return count($items);
    }

    public function getDoneItemCnt () {
        $done_items = CerticanItemDao::getDoneListByCertican($this);

        return count($done_items);
    }

    public function getStatuStr () {
        $cnt = $this->getDoneItemCnt();

        if ($cnt < 21) {
            return "进行中";
        } else {
            return "已结束";
        }
    }
}
