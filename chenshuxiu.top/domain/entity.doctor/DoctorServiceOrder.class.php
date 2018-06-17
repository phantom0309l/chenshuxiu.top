<?php
/*
 * DoctorServiceOrder
 */
class DoctorServiceOrder extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'doctorid'    //doctorid
        ,'doctorserviceordertplid'    //doctorserviceordertplid
        ,'objtype'    //objtype
        ,'objid'    //objid
        ,'objcode'    //objcode
        ,'amount'    //服务费用, 单位分
        ,'the_month'    //the_month
        ,'week_from_begin'    //从2015-03-23开始的周数
        ,'from_date'    //from_date
        ,'end_date'    //end_date
        ,'doctor_is_confirmed'    //医生是否确认
        ,'doctor_time_confirmed'    //医生确认时间
        ,'content'    //内容
        ,'is_recharge' //是否充值到医生账户。0：否，1：是
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('doctorid' ,'doctorserviceordertplid' ,'objid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
        $this->_belongtos["doctorserviceordertpl"] = array ("type" => "DoctorServiceOrderTpl", "key" => "doctorserviceordertplid" );
        $this->_belongtos["obj"] = array ("type" => $this->objtype, "key" => "objid" );
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["doctorserviceordertplid"] = $doctorserviceordertplid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    // $row["amount"] = $amount;
    // $row["the_month"] = $the_month;
    // $row["week_from_begin"] = $week_from_begin;
    // $row["from_date"] = $from_date;
    // $row["end_date"] = $end_date;
    // $row["doctor_is_confirmed"] = $doctor_is_confirmed;
    // $row["doctor_time_confirmed"] = $doctor_time_confirmed;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"DoctorServiceOrder::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] =  0;
        $default["doctorserviceordertplid"] =  0;
        $default["objtype"] = '';
        $default["objid"] =  0;
        $default["objcode"] = '';
        $default["amount"] =  0;
        $default["the_month"] = '';
        $default["week_from_begin"] =  0;
        $default["from_date"] = '';
        $default["end_date"] = '';
        $default["doctor_is_confirmed"] =  0;
        $default["doctor_time_confirmed"] = '';
        $default["content"] = '';
        $default["is_recharge"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getAmount_yuan () {
        return sprintf("%.2f", $this->amount / 100);
    }

    // 充值转账
    public function recharge () {
        if (0 == $this->is_recharge) {
            $sysAccount = Account::getSysAccount('sys_doctor_income_fund');
            $doctor = $this->doctor;
            DBC::requireTrue($doctor instanceof Doctor, "医生不存在");
            $user = $doctor->user;
            DBC::requireTrue($user instanceof User, "user不存在");

            $userAccount = $user->getAccount('doctor_rmb');
            $sysAccount->transto($userAccount, $this->amount, $this, 'process', '医事服务费');
            $this->is_recharge = 1;
        }
    }

    public function isRecharged(){
        return 1 == $this->is_recharge;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
