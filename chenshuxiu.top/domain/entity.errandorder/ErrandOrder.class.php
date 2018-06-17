<?php

/*
 * ErrandOrder
 */

class ErrandOrder extends Entity implements PayHandle
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'shopaddressid'    //shopaddressid
        , 'is_use_ybk'    //是否使用医保卡
        , 'content'    //药品、数量信息
        , 'amount'    //订单总金额,包括配送费, 单位分
        , 'refund_amount'    //已退款金额,单位分
        , 'is_pay'    //已经支付
        , 'time_pay'    //支付时间
        , 'time_refund'    //退款时间
        , 'pos'    //第几单
        , 'status'    //是否有效
        , 'audit_remark'    //运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'shopaddressid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["shopaddress"] = array("type" => "ShopAddress", "key" => "shopaddressid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["shopaddressid"] = $shopaddressid;
    // $row["is_use_ybk"] = $is_use_ybk;
    // $row["content"] = $content;
    // $row["amount"] = $amount;
    // $row["refund_amount"] = $refund_amount;
    // $row["is_pay"] = $is_pay;
    // $row["time_pay"] = $time_pay;
    // $row["time_refund"] = $time_refund;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    // $row["audit_remark"] = $audit_remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ErrandOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["shopaddressid"] = 0;
        $default["is_use_ybk"] = 0;
        $default["content"] = '';
        $default["amount"] = 20000;
        $default["refund_amount"] = 0;
        $default["is_pay"] = 0;
        $default["time_pay"] = '';
        $default["time_refund"] = '';
        $default["pos"] = 0;
        $default["status"] = 1;
        $default["audit_remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getAllStatus() {
        return [
            0 => '全部',
            1 => '待支付',
            2 => '已支付',
            3 => '已支付，有退款',
        ];
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 总金额 (元), 包括快递费
    public function getAmount_yuan() {
        return sprintf("%.2f", $this->amount / 100);
    }

    // 剩余金额 (分)
    public function getLeft_amount () {
        return $this->amount - $this->refund_amount;
    }

    // 尝试支付
    public function tryPay(Account $rmbAccount) {
        // 尚待支付, 去支付
        if (0 == $this->is_pay) {
            if ($rmbAccount->balance >= $this->amount) {
                $sysAccount = Account::getSysAccount('sys_user_shop_out');

                $remark = "代购药品订单[{$this->id}]支付";

                $rmbAccount->transto($sysAccount, $this->amount, $this, 'pay', $remark);
                $this->is_pay = 1;
                $this->time_pay = XDateTime::now();

                $pos = ErrandOrderDao::getIsPayErrandOrderCntByPatient($this->patient) + 1;
                $this->pos = $pos;

                // 生成任务
                OpTaskService::createPatientOpTask($this->patient, 'order:ErrandOrder', $this);

                // 给运营推送消息
                $content = "Price[{$this->amount}]Patient[{$this->patient->name}]Doctor[{$this->patient->doctor->name}]成功支付订单,ErrandOrder[{$this->id}]";
                PushMsgService::sendMsgToAuditorBySystem('ErrandOrder', 1, $content);

                $shopAddress = $this->shopaddress;
                if ($this->is_use_ybk && $shopAddress instanceof ShopAddress) {
                    // 给患者推送收货地址信息
                    $content = "收货人：{$shopAddress->linkman_name}\n联系电话：{$shopAddress->linkman_mobile}\n邮寄地址：{$shopAddress->getDetailAddress()}";
                    PushMsgService::sendTxtMsgToPatientBySystem($this->patient, $content);
                }

                // 入流
                $pipe = Pipe::createByEntity($this, 'pay', $this->wxuserid);

                Debug::warn("成功支付代购药品订单 ErrandOrder[{$this->id}]->amount = {$this->amount}, rmbAccount->balance = {$rmbAccount->balance} ");
            } else {
                Debug::warn("ErrandOrder[{$this->id}]支付失败, 余额不足, {$rmbAccount->balance} < {$this->amount}");
            }
        } else {
            Debug::warn("ErrandOrder[{$this->id}]已支付了, 不用再支付了");
        }
    }

    public function getWxPayUnifiedOrder_Body () {
        $str = "订单ID" . $this->id;
        return $str;
    }

    public function getWxPayUnifiedOrder_Attach () {
        return "购买了代您开药";
    }

    public function getPayAmount () {
        return $this->amount;
    }

    // 是否可以退款
    public function canRefund() {
        // 有效、已支付，且未完全退款
        if ($this->status == 1 && $this->is_pay == 1 && $this->refund_amount < $this->amount) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 退款至原支付账户, 已支付状态
     *
     * @return bool
     */
    public function refund() {
        // 未支付或已退款
        if (!$this->is_pay) {
            return false;
        }

        $left_amount = $this->getLeft_amount();

        if ($left_amount < 1) {
            return false;
        }

        $amount = $this->amount;

        DBC::requireTrue($amount <= $left_amount, "退款金额:{$amount}不能超过,可退金额:{$left_amount}");

        if ($amount < 0) {
            $amount = 0;
            return false;
        }

        $remark = "代您开药[{$this->id}]退款至余额";

        $sysAccount = Account::getSysAccount('sys_user_shop_out');
        $userRmbAccount = $this->user->getAccount('user_rmb');

        $code = 'refund';

        $sysAccount->transto($userRmbAccount, $amount, $this, $code, $remark);

        // 根据提现单生成退款单
        OrderService::processAccountWithdrawRefund($userRmbAccount, Auditor::getSystemAuditor());

        $this->time_refund = XDateTime::now();

        $this->refund_amount += $amount;

        return true;
    }

}
