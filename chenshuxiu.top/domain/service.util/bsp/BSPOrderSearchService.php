<?php

//订单结果查询接口
class BSPOrderSearchService extends BaseBSP
{
    public function OrderSearch($orderid) {
        $OrderSearch = '<OrderSearch orderid="'.$orderid.'" />';
        $data = $this->postXmlBodyWithVerify($OrderSearch);
        return $this->OrderSearchResponse($data);
    }

    private function OrderSearchResponse($data) {
        return $this->getResponse($data, 'OrderResponse');
    }

}
