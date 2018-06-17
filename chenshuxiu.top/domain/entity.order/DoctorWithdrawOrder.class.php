<?php
/*
 * DoctorWithdrawOrder
 */
class DoctorWithdrawOrder extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'userid'    //userid
        ,'doctorid'    //doctorid
        ,'amount'    //金额,分
        ,'status'    //退款状态 0 等待 1 成功 2 撤销
        ,'donetime'    //该订单的结束时间 撤销 或者成功退款
        ,'auditorid'    //审核人员id
        ,'remark'    //备注
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('userid' ,'doctorid' ,'amount' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["doctorid"] = $doctorid;
    // $row["amount"] = $amount;
    // $row["status"] = $status;
    // $row["donetime"] = $donetime;
    // $row["auditorid"] = $auditorid;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"DoctorWithdrawOrder::createByBiz row cannot empty");

        $default = array();
        $default["userid"] =  0;
        $default["doctorid"] =  0;
        $default["amount"] =  0;
        $default["status"] =  0;
        $default["donetime"] = '0000-00-00 00:00:00';
        $default["auditorid"] =  0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    //通过
    public function pass(){
        $this->status = 1;
        $this->donetime = date("Y-m-d H:i:s", time());
    }

    public function getAmount_yuan () {
        return sprintf("%.2f", $this->amount / 100);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
