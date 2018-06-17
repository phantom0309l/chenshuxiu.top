<?php

class FangcunWxPayNotify extends WxPayNotify
{

    // 重载
    public function NotifyProcess ($data, &$msg) {
        Debug::trace("========== NotifyProcess[data] : " . print_r($data, true) . " ==========");

        $out_trade_no = $data["out_trade_no"];
        $total_fee = $data["total_fee"];

        $depositeOrder = DepositeOrderDao::getDepositeOrderByfangcun_trade_no($out_trade_no);

        Debug::trace("========== out_trade_no[{$out_trade_no}] , depositeOrder[{$depositeOrder->id}] ==========");

        if (false == $depositeOrder instanceof DepositeOrder) {
            $msg = "订单不存在";
            Debug::warn("========== DepositeOrder不存在, out_trade_no = {$out_trade_no} ==========");
            return false;
        }

        // 先记录调用时间和次数
        $depositeOrder->notify_cnt += 1;
        $depositeOrder->last_notify_time = date("Y-m-d H:i:s");

        // 价格不对严重问题
        if ($depositeOrder->amount != $total_fee) {
            $str = "DepositeOrder[{$depositeOrder->id}]->amount : {$depositeOrder->amount} <> total_fee : {$total_fee}   ";
            Debug::warn("========== {$str} ==========");

            $msg = "金额不匹配";
            $depositeOrder->notify_last_errcode = "FEE_NOT_MATCH";
            return false;
        }

        // 尚未置支付状态
        if (0 == $depositeOrder->recharge_status) {
            // 第三方平台交易订单号
            $depositeOrder->pay_platform_trade_no = $data["transaction_id"];
            $depositeOrder->notify_time = date("Y-m-d H:i:s");
            $depositeOrder->notify_result_code = $data["result_code"];

            // 充值成功时,进行转账
            if ($data["result_code"] == 'SUCCESS') {
                $depositeOrder->recharge();
            }
        }

        // 尝试进行后续处理, 也许已经处理过了
        if (1 == $depositeOrder->recharge_status) {
            $depositeOrder->tryProcessObj();
        }

        return true;
    }
}
