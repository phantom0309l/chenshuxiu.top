<?php

/**
 * 该接口用于：
 * 客户在确定将货物交付给顺丰托运后，将运单上的一些重要信息，如快件重量通过此接口发送给顺丰。
 * 客户在发货前取消订单。
 * 注意：订单取消之后，订单号也是不能重复利用的。
 */
class BSPOrderConfirmService extends BaseBSP
{
    /**
     * 确认订单
     * @param $orderid
     * @param $mailno
     * @param array $options
     * @return array|bool
     */
    public function OrderConfirm($orderid, $mailno, $options=array()) {
        return $this->OrderConfirmRequest($orderid, $mailno, 1, $options);
    }

    /**
     * 取消订单
     * @param $orderid
     * @param string $mailno
     * @param array $options
     * @return array|bool
     */
    public function OrderCancel($orderid, $mailno='', $options=array()) {
        return $this->OrderConfirmRequest($orderid, $mailno, 2, $options);
    }

    public function OrderConfirmRequest($orderid, $mailno, $dealtype, $options=array()) {
        $params = array();
        $params['dealtype'] = $dealtype;
        $params['orderid'] = $orderid;
        $params['mailno '] = $mailno;

        $order_params = $this->paramsToString($params);
        $addedServices_str = count($options) > 0 ? $this->paramsToString($options, 'OrderConfirmOption') : '';
        $xml_string = "<OrderConfirm$order_params>$addedServices_str</OrderConfirm>";
        $data = $this->postXmlBodyWithVerify($xml_string);
        return $this->OrderConfirmResponse($data);
    }

    private function  OrderConfirmResponse($data) {
        return $this->getResponse($data, 'OrderConfirmResponse');
    }

}
