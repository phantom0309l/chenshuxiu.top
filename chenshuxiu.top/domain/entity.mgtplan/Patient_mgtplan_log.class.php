<?php
/*
 * Patient_mgtplan_log
 */
class Patient_mgtplan_log extends Entity
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
        ,'mgtplanid'    //mgtplanid
        ,'type'    //操作类型
        ,'auditorid'    //auditorid
        ,'remark'    //remark
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('wxuserid' ,'userid' ,'patientid' ,'mgtplanid' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["mgtplan"] = array ("type" => "MgtPlan", "key" => "mgtplanid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["mgtplanid"] = $mgtplanid;
    // $row["type"] = $type;
    // $row["auditorid"] = $auditorid;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"Patient_mgtplan_log::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["mgtplanid"] =  0;
        $default["type"] = '';
        $default["auditorid"] =  0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
