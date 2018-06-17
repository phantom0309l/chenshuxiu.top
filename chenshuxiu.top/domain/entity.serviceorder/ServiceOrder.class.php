<?php

/*
 * ServiceOrder
 */

class ServiceOrder extends Entity implements PayHandle
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'serviceproductid'    //serviceproductid
        , 'serviceproduct_type'    //serviceproduct_type
        , 'amount'    //订单总金额, 单位分
        , 'refund_amount'    //已退款金额,单位分
        , 'is_pay'    //是否支付
        , 'time_submit'    //下单时间
        , 'time_pay'    //支付时间
        , 'time_refund'    //退款时间
        , 'pos'    //第几单
        , 'remark'    //运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'serviceproductid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["serviceproduct"] = array("type" => "ServiceProduct", "key" => "serviceproductid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["serviceproductid"] = $serviceproductid;
    // $row["serviceproduct_type"] = $serviceproduct_type;
    // $row["amount"] = $amount;
    // $row["refund_amount"] = $refund_amount;
    // $row["is_pay"] = $is_pay;
    // $row["time_submit"] = $time_submit;
    // $row["time_pay"] = $time_pay;
    // $row["time_refund"] = $time_refund;
    // $row["pos"] = $pos;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ServiceOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["serviceproductid"] = 0;
        $default["serviceproduct_type"] = '';
        $default["amount"] = 0;
        $default["refund_amount"] = 0;
        $default["is_pay"] = 0;
        $default["time_submit"] = date('Y-m-d H:i:s');
        $default["time_pay"] = '0000-00-00 00:00:00';
        $default["time_refund"] = '0000-00-00 00:00:00';
        $default["pos"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getAllStatus() {
        return [
            0 => '全部',
            1 => '未支付',
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

    // 订单退款金额 (元)
    public function getRefund_amount_yuan() {
        return sprintf("%.2f", $this->refund_amount / 100);
    }

    // 剩余金额 (分)
    public function getLeft_amount() {
        return $this->amount - $this->refund_amount;
    }

    // 剩余金额 (元)
    public function getLeft_amount_yuan() {
        return sprintf("%.2f", $this->getLeft_amount() / 100);
    }

    // 是否退款
    public function getRefundStr() {
        if (!$this->is_pay) {
            return '--';
        }

        $str = "";

        if ($this->getLeft_amount() < 1) {
            $str = "<span class='green'>全额退款</span>";
        } elseif ($this->refund_amount > 0) {
            $str = "<span class='green'>已退款(" . $this->getRefund_amount_yuan() . ")元</span>";
        } else {
            $str = '<span class="gray">未退款</span>';
        }

        return $str;
    }

    // 退款, 已支付状态
    public function refund($amount, $remark = '') {
        if (false == $this->is_pay) {
            return false;
        }

        $left_amount = $this->getLeft_amount();

        if ($left_amount < 1) {
            return false;
        }

        DBC::requireTrue($amount <= $left_amount, "退款金额:{$amount}不能超过,可退金额:{$left_amount}");

        if ($amount < 0) {
            $amount = 0;
            return false;
        }

        $this->refund_amount += $amount;

        if (empty($remark)) {
            $remark = "服务类订单[{$this->id}]退款至余额";
        }

        $sysAccount = Account::getSysAccount('sys_user_shop_out');
        $userRmbAccount = $this->user->getAccount('user_rmb');

        // 部分退款
        if ($amount < $this->amount) {
            $cnt = $this->getRefundAccountTransCnt();
            $code = "refund:" . ($cnt + 1);
        } else {
            $code = 'refund';
        }

        $sysAccount->transto($userRmbAccount, $amount, $this, $code, $remark);

        // 根据提现单生成退款单
        OrderService::processAccountWithdrawRefund($userRmbAccount, Auditor::getSystemAuditor());

        $this->time_refund = XDateTime::now();

        return true;
    }

    public function getRefundAccountTransCnt() {
        $sql = "select count(*) from accounttranss where objtype='ShopOrder' and objid=:objid and code like 'refund%';";
        $bind = [];
        $bind[':objid'] = $this->id;
        return 0 + Dao::queryValue($sql, $bind);
    }

    //尝试支付
    public function tryPay(Account $rmbAccount) {
        // 尚未支付, 去支付
        if (0 == $this->is_pay) {
            if ($rmbAccount->balance >= $this->amount) {
                $patient = $this->patient;
                $doctor = $patient->doctor;

                $type = $this->serviceproduct_type;

                $sysAccount = Account::getSysAccount('sys_user_shop_out');

                $remark = "{$type}订单[{$this->id}]支付";

                $rmbAccount->transto($sysAccount, $this->amount, $this, 'pay', $remark);
                $this->is_pay = 1;
                $this->time_pay = XDateTime::now();
                $this->pos = ServiceOrderDao::getIsPayServiceOrderCntByPatientAndType($patient, $this->serviceproduct_type) + 1;

                $this->remark = "Price[{$this->amount}]Patient[{$patient->name}]Doctor[{$doctor->name}]成功支付{$type}订单";;

                // 创建支付成功流
                $pipe = Pipe::createByEntity($this, 'pay', $this->wxuserid);

                switch ($type) {
                    case 'quickpass':
                        QuickPass_ServiceItem::tryPay($this);
                        break;
                    default:
                        break;
                }

                Debug::warn("成功支付{$type}订单 ServiceOrder[{$this->id}]->amount = {$this->amount}, rmbAccount->balance = {$rmbAccount->balance} ");
            } else {
                Debug::warn("ServiceOrder[{$this->id}]支付失败, 余额不足, {$rmbAccount->balance} < {$this->amount}");
            }
        } else {
            Debug::warn("ServiceOrder[{$this->id}]已支付了, 不用再支付了");
        }
    }

    public function getWxPayUnifiedOrder_Body () {
        $str = "订单ID" . $this->id;
        return $str;
    }

    public function getWxPayUnifiedOrder_Attach () {
        return "购买了快速通行证";
    }

    public function getPayAmount () {
        return $this->amount;
    }

    public function getEndTime() {
        $quickpass_serviceitem = QuickPass_ServiceItemDao::getLastValidOneByServiceOrderid($this->id);
        if ($quickpass_serviceitem instanceof QuickPass_ServiceItem) {
            return $quickpass_serviceitem->endtime;
        }

        return '未知';
    }

    public function getItems() {
        switch ($this->serviceproduct_type) {
            case 'quickpass':
                return QuickPass_ServiceItemDao::getListByServiceOrderid($this->id);
            default:
                return [];
        }
    }
}
