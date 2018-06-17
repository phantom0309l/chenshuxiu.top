<?php

// 创建: 20171214 by txj
class WxPayService
{
    //防止重复支付，通过微信orderQuery接口查询业务order是否已经支付
    //微信文档：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2
    public static function isPay(Entity $order){
        $depositeOrders = DepositeOrderDao::getDepositeOrderListByEntity($order);
        foreach($depositeOrders as $depositeOrder){
            $input = new WxPayOrderQuery();
            $input->SetOut_trade_no($depositeOrder->fangcun_trade_no);
            $result = WxPayApi::orderQuery($input);
            $trade_state = isset($result['trade_state']) ? $result['trade_state'] : '';
            if($trade_state == "SUCCESS" || $trade_state == "USERPAYING"){
                return true;
            }
        }
        return false;
    }

}
