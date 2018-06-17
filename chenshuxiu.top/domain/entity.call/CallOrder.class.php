<?php

/*
 * CallOrder
 */

class CallOrder extends Entity implements PayHandle
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'the_doctorid'    //生成订单时的doctorid
        , 'cdrmeetingid'    //cdrmeetingid
        , 'callproductid'    //callproductid
        , 'patient_mobile'    //患者手机号
        , 'call_duration'   // 通话时长，单位秒
        , 'amount'    //订单总金额,包括配送费, 单位分
        , 'is_pay'    //已经支付
        , 'time_pay'    //支付时间
        , 'status'    //0：初始化，1：患者未接听，2：医生未接听，3：双方已接听，4：其他错误
        , 'remark'    //运用备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'the_doctorid', 'cdrmeetingid', 'callproductid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["the_doctor"] = array("type" => "Doctor", "key" => "the_doctorid");
        $this->_belongtos["cdrmeeting"] = array("type" => "CdrMeeting", "key" => "cdrmeetingid");
        $this->_belongtos["callproduct"] = array("type" => "CallProduct", "key" => "callproductid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["the_doctorid"] = $the_doctorid;
    // $row["cdrmeetingid"] = $cdrmeetingid;
    // $row["callproductid"] = $callproductid;
    // $row["patient_mobile"] = $patient_mobile;
    // $row["call_duration"] = $call_duration;
    // $row["amount"] = $amount;
    // $row["is_pay"] = $is_pay;
    // $row["time_pay"] = $time_pay;
    // $row["status"] = $status;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "CallOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["the_doctorid"] = 0;
        $default["cdrmeetingid"] = 0;
        $default["callproductid"] = 0;
        $default["patient_mobile"] = '';
        $default["call_duration"] = 0;
        $default["amount"] = 0;
        $default["is_pay"] = 0;
        $default["time_pay"] = '';
        $default["status"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 总金额 (元), 包括快递费
    public function getAmount_yuan() {
        return sprintf("%.2f", $this->amount / 100);
    }

    // 向上取整
    public function getCeilCallDurationMinute() {
        return ceil($this->call_duration / 60);
    }

    public function getStatusDesc() {
        switch ($this->status) {
            case 0:
                return "初始化";
            case 1:
                return "患者未接听";
            case 2:
                return "医生未接听";
            case 3:
                return "双方已接听";
            case 4:
                return "其他错误";
            default:
                return "";
        }
    }

    //尝试支付
    public function tryPay(Account $rmbAccount) {
        // 尚未支付, 去支付
        if (0 == $this->is_pay) {
            if ($rmbAccount->balance >= $this->amount) {
                $sysAccount = Account::getSysAccount('sys_user_shop_out');

                $remark = "紧急电话订单[{$this->id}]支付";

                $rmbAccount->transto($sysAccount, $this->amount, $this, 'pay', $remark);
                $this->is_pay = 1;
                $this->time_pay = XDateTime::now();

                $content = "Price[{$this->amount}]Patient[{$this->patient->name}]Doctor[{$this->the_doctor->name}]成功支付紧急电话订单";
                PushMsgService::sendMsgToAuditorBySystem('CallOrder', 1, $content);

                $this->remark = $content;

                // 创建支付成功流
                $pipe = Pipe::createByEntity($this, 'pay', $this->wxuserid);

                Debug::warn("成功支付紧急电话订单 CallOrder[{$this->id}]->amount = {$this->amount}, rmbAccount->balance = {$rmbAccount->balance} ");
            } else {
                Debug::warn("CallOrder[{$this->id}]支付失败, 余额不足, {$rmbAccount->balance} < {$this->amount}");
            }
        } else {
            Debug::warn("CallOrder[{$this->id}]已支付了, 不用再支付了");
        }
    }

    public function getWxPayUnifiedOrder_Body () {
        $str = "订单ID" . $this->id;
        return $str;
    }

    public function getWxPayUnifiedOrder_Attach () {
        return "使用了紧急电话服务";
    }

    public function getPayAmount () {
        return $this->amount;
    }

}
