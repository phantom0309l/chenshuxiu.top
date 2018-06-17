<?php

class OrderService
{

    public static function processDoctorWithdraw(Account $user_rmbAccount, $amount, Auditor $auditor){

        $user = $user_rmbAccount->user;
        $doctor = $user->getDoctor();
        $total_amount = $user_rmbAccount->balance;

        if($amount > $total_amount){
            Debug::warn("{$doctor->name}提现金额[{$amount}]大于余额[{$total_amount}]");
            return;
        }

        $row = array();
        $row["userid"] = $user->id;
        $row["doctorid"] = $doctor->id;
        $row["amount"] = $amount;
        $row["auditorid"] = $auditor->id;

        // 生成提现单
        $doctorWithdrawOrder = DoctorWithdrawOrder::createByBiz($row);

        // 余额转账至sys_doctor_withdraw_out
        $sysAccount = Account::getSysAccount('sys_doctor_withdraw_out');
        $accountTrans = $user_rmbAccount->transto($sysAccount, $amount, $doctorWithdrawOrder, 'process', "提现(汇款给医生)");
        return $doctorWithdrawOrder;
    }

    // 根据提现单生成原路退款单
    public static function processAccountWithdrawRefund (Account $user_rmbAccount, Auditor $auditor) {
        $sysAccount = Account::getSysAccount('sys_user_refund_out');

        $userid = $user_rmbAccount->userid;
        $total_amount = $user_rmbAccount->balance;

        $row = array();
        $row["wxuserid"] = 0;
        $row["userid"] = $userid;
        $row["patientid"] = 0;
        $row["amount"] = $total_amount;
        $row["auditorid"] = $auditor->id;

        // 生成提现单
        $patientWithdrawOrder = PatientWithdrawOrder::createByBiz($row);
        $patientWithdrawOrder->pass();

        // 冻结账号
        $user_rmb_freezeAccount = Account::getByUserAndCode($user_rmbAccount->user, 'user_rmb_freeze');

        // 余额转账至冻结账号
        $accountTrans = $user_rmbAccount->transto($user_rmb_freezeAccount, $total_amount, $patientWithdrawOrder, 'process', "提现(原路退款)");

        // 冻结账号的余额
        $total_amount = $user_rmb_freezeAccount->balance;

        // 需要退的金额
        $left_amount = $total_amount;

        $depositeOrders = DepositeOrderDao::getDepositeOrderListForRefundByUseridAmount($userid, $total_amount);

        // 生成退款单
        $refundOrders = array();
        foreach ($depositeOrders as $depositeOrder) {
            // 退完了
            if ($left_amount < 1) {
                break;
            }

            // depositeOrder 能退金额
            $diff_amount = $depositeOrder->amount - $depositeOrder->refund_amount;
            if ($diff_amount > $left_amount) {
                // 额度用不完
                $diff_amount = $left_amount;
            }

            $row = array();
            $row["userid"] = $userid;
            $row["amount"] = $diff_amount;
            $row["depositeorderid"] = $depositeOrder->id;
            $row["patientwithdraworderid"] = $patientWithdrawOrder->id;

            $refundOrder = RefundOrder::createByBiz($row);
            $refundOrders[] = $refundOrder;

            // 执行退款单
            $result = $refundOrder->process();
            if ($result) {
                // depositeOrder 已退款金额
                $depositeOrder->refund_amount += $diff_amount;

                // 还需要退的金额
                $left_amount -= $diff_amount;

                // 转账
                $accountTrans = $user_rmb_freezeAccount->transto($sysAccount, $refundOrder->amount, $refundOrder, 'process',
                        "原路退款,充值单号:{$depositeOrder->fangcun_trade_no}");
            } else {
                // 退款失败
                Debug::warn("退款失败,RefundOrder[{$refundOrder->id}]");
            }
        }
    }

    //从冻结账户退款
    //当调用微信退款接口失败时调用，此时RefundOrder已经生成。
    public static function processFreezeAccountWithdrawRefund (User $user, RefundOrder $refundOrder) {
        $sysAccount = Account::getSysAccount('sys_user_refund_out');
        // 冻结账号
        $user_rmb_freezeAccount = Account::getByUserAndCode($user, 'user_rmb_freeze');

        //要退的金额
        $amount = $refundOrder->amount;
        // depositeOrder 能退金额
        $depositeOrder = $refundOrder->depositeorder;
        $diff_amount = $depositeOrder->amount - $depositeOrder->refund_amount;
        if($diff_amount < $amount){
            Debug::warn("DepositeOrder[{$depositeOrder->id}],不足以退款[{$amount}], RefundOrder[{$refundOrder->id}]");
            return;
        }

        // 执行退款单
        $result = $refundOrder->process();
        if ($result) {
            $depositeOrder->refund_amount += $amount;
            // 转账
            $accountTrans = $user_rmb_freezeAccount->transto($sysAccount, $amount, $refundOrder, 'process',
                    "原路退款,充值单号:{$depositeOrder->fangcun_trade_no}");
        } else {
            // 退款失败
            Debug::warn("退款失败,RefundOrder[{$refundOrder->id}]");
        }

    }

    //小儿多动症商户平台
    //为H5页面发起支付做准备
    public static function processForH5Pay_ADHD ($wxuser, $myuser, $mypatient, $obj, $pay_openid) {
        return self::processForH5PayImp($wxuser, $myuser, $mypatient, $obj, $pay_openid, WxShop::WxShopId_ADHD, "/paygate/WxPayNotify");
    }

    //肿瘤商户平台
    //为H5页面发起支付做准备
    public static function processForH5Pay_Cancer ($wxuser, $myuser, $mypatient, $obj, $pay_openid) {
        return self::processForH5PayImp($wxuser, $myuser, $mypatient, $obj, $pay_openid, WxShop::WxShopId_Cancer, "/paygate/WxPayNotify19");
    }

    //为H5页面发起支付做准备
    public static function processForH5PayImp ($wxuser, $myuser, $mypatient, $obj, $pay_openid, $the_wxshopid, $paygate_path) {
        // 患者人民币账户
        $myAccount = Account::getByUserAndCode($myuser, 'user_rmb');

        $pay_amount = $obj->getPayAmount();

        $row = array();
        $row["wxuserid"] = $wxuser->id;
        $row["userid"] = $myuser->id;
        $row["patientid"] = $mypatient->id;
        $row["accountid"] = $myAccount->id;
        $row["pay_wxshopid"] = $the_wxshopid;
        $row["objtype"] = get_class($obj);
        $row["objid"] = $obj->id;
        $row["amount"] = $pay_amount;
        $depositeOrder = DepositeOrder::createByBiz($row);

        $api_uri = Config::getConfig("api_uri");

        $input = new WxPayUnifiedOrder();
        $input->SetBody($obj->getWxPayUnifiedOrder_Body());
        $input->SetAttach($obj->getWxPayUnifiedOrder_Attach());
        $input->SetOut_trade_no($depositeOrder->fangcun_trade_no);
        $input->SetTotal_fee($pay_amount);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url($api_uri . $paygate_path);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($pay_openid);

        $wxshop = WxShop::getById($the_wxshopid);
        $wxshop->initWxPayConfig();

        $unifiedOrderResult = WxPayApi::unifiedOrder($input);

        $depositeOrder->wx_prepay_id = $unifiedOrderResult['prepay_id'];
        $depositeOrder->wx_unifiedorder_result_code = $unifiedOrderResult['result_code'];

        $result = [];
        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($unifiedOrderResult);
        $result["jsApiParameters"] = json_decode($jsApiParameters, true);
        $result["fangcun_trade_no"] = $depositeOrder->fangcun_trade_no;

        return $result;
    }

}
