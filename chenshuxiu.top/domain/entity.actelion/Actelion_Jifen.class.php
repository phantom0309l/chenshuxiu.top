<?php

/*
 * Actelion_Jifen
 */
class Actelion_Jifen extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'jifen_amount'    //明细金额
        ,'jifen_balance'    //积分余额
        ,'objtype'    //三元式
        ,'objid'    //三元式
        ,'code'    //三元式
        ,'remark'    //备注
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid' ,'userid' ,'patientid' ,'objid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["obj"] = array ("type" => $this->objtype, "key" => "objid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["jifen_amount"] = $jifen_amount;
    // $row["jifen_balance"] = $jifen_balance;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["code"] = $code;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Actelion_Jifen::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["jifen_amount"] =  0;
        $default["jifen_balance"] =  0;
        $default["objtype"] = '';
        $default["objid"] =  0;
        $default["code"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
