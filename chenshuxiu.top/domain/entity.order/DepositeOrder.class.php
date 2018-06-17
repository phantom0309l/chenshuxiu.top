<?php
// DepositeOrder
// 充值单

// owner by sjp
// create by sjp
// modify by sjp 20170713
class DepositeOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'accountid',  // 收益账户
            'pay_wxshopid', //商户平台对应的wxshopid
            'objtype',  // 关联单据类型
            'objid',  // 关联单据id
            'amount',  // 金额,分
            'refund_amount',  // 已退款金额,分
            'recharge_type',  // 支付方式
            'recharge_status',  // 充值状态
            'fangcun_trade_no',  // 方寸订单号
            'pay_platform_trade_no',  // 第三方支付平台订单号
            'wx_prepay_id',  // 微信 prepay_id
            'wx_unifiedorder_result_code',  // 微信统一下单,返回的结果
            'return_time',  // 客户端同步返回时间
            'return_result_code',  // 客户端同步回调结果
            'notify_time',  // 首次正确异步通知的时间: recharge_status=1后, 就不能改了
            'notify_result_code',  // 异步回调结果: recharge_status=1后, 就不能改了
            'last_notify_time',  // 末次异步通知时间
            'last_notify_result_code',  // 末次异步通知结果
            'notify_cnt',  // 异步通知次数
            'notify_last_errcode',  // 异步通知的末次未成功的错误信息
            'orderquery_time',  // 主动查询时间, recharge_status=0, 发起主动查询
            'orderquery_response_content',  // 主动查询, 结果内容
            'orderquery_result_code',  // 主动查询, 结果
            'orderquery_trade_state',  // 主动查询, 交易状态
            'orderquery_trade_state_desc',  // 主动查询, 交易状态描述
            'remark'); // 订单备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid',
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["pay_wxshop"] = array(
            "type" => "WxShop",
            "key" => "pay_wxshopid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["account"] = array(
            "type" => "Account",
            "key" => "accountid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["accountid"] = $accountid;
    // $row["pay_wxshopid"] = $pay_wxshopid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["amount"] = $amount;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Depositeorder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["accountid"] = '';
        $default["pay_wxshopid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["amount"] = 0;
        $default["refund_amount"] = 0;
        $default["recharge_type"] = '';
        $default["recharge_status"] = 0;
        $default["fangcun_trade_no"] = '';
        $default["pay_platform_trade_no"] = '';
        $default["wx_prepay_id"] = '';
        $default["wx_unifiedorder_result_code"] = '';
        $default["return_time"] = '0000-00-00 00:00:00';
        $default["return_result_code"] = '';
        $default["notify_time"] = '0000-00-00 00:00:00';
        $default["notify_result_code"] = '';
        $default["last_notify_time"] = '0000-00-00 00:00:00';
        $default["last_notify_result_code"] = '';
        $default["notify_cnt"] = 0;
        $default["notify_last_errcode"] = '';
        $default["orderquery_time"] = '0000-00-00 00:00:00';
        $default["orderquery_response_content"] = '';
        $default["orderquery_result_code"] = '';
        $default["orderquery_trade_state"] = '';
        $default["orderquery_trade_state_desc"] = '';
        $default["remark"] = '';

        $row += $default;
        $entity = new self($row);

        $ymd = date('Ymd');

        $entity->fangcun_trade_no = "{$ymd}_{$entity->objid}_{$entity->id}";

        return $entity;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getNotify_mdHi () {
        return substr($this->notify_time, 5, 11);
    }

    // 金额 (元)
    public function getAmount_yuan () {
        return sprintf("%.2f", $this->amount / 100);
    }

    // 充值转账
    public function recharge () {

        // 尚未置支付状态, 充值成功时, 进行转账
        if (0 == $this->recharge_status) {
            $sysAccount = Account::getSysAccount('sys_user_deposite_fund');
            $userAccount = $this->account;
            $sysAccount->transto($userAccount, $this->amount, $this, 'process', '微信支付充值');
            $this->recharge_status = 1;
        }
    }

    // 尝试处理关联对象
    public function tryProcessObj () {
        $rmbAccount = $this->account;

        try {
            $this->obj->tryPay($rmbAccount);
        } catch (Exception $e) {
            Debug::error('[支付回调]obj未实现PayHandle接口');
        }

//        if ($this->obj instanceof ShopOrder) {
//            $this->obj->tryPay($rmbAccount);
//        } elseif ($this->obj instanceof CallOrder) {
//            $this->obj->tryPay($rmbAccount);
//        } elseif ($this->obj instanceof QuickConsultOrder) {
//            $this->obj->tryPay($rmbAccount);
//        } elseif ($this->obj instanceof ServiceOrder) {
//            $this->obj->tryPay($rmbAccount);
//        } elseif ($this->obj instanceof ErrandOrder) {
//            $this->obj->tryPay($rmbAccount);
//        }
    }
}
