<?php
/*
 * PatientDrugState
 */
class PatientDrugState extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    //服药
    const state_ondrug = 'ondrug';

    //不服
    const state_nodrug = 'nodrug';

    //停药
    const state_stopdrug = 'stopdrug';

    //未知
    const state_unknown = 'unknown';

    public static function getKeysDefine()
    {
        return  array(
        'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'baodao_date'    //baodao_date
        ,'pos'    //pos
        ,'state'    //状态，unknown : 未知; ondrug : 服药; stopdrug : 停药; nodrug : 不服
        ,'content'    //文本内容
        ,'offset_daycnt'    //距离报到天数
        ,'auditorid'    //auditorid
        ,'remark'    //运营备注
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('wxuserid' ,'userid' ,'patientid' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["baodao_date"] = $baodao_date;
    // $row["pos"] = $pos;
    // $row["state"] = $state;
    // $row["content"] = $content;
    // $row["offset_daycnt"] = $offset_daycnt;
    // $row["auditorid"] = $auditorid;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"PatientDrugState::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["baodao_date"] = '';
        $default["pos"] = 1;
        $default["state"] = '';
        $default["content"] = '';
        $default["offset_daycnt"] =  0;
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
