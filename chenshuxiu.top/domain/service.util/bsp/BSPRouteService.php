<?php

class BSPRouteService extends BaseBSP
{
    /**
     * 订单路由查询接口
     * @param $tracking_type 查询号类别 1:顺丰运单号 2:客户订单号
     * @param string $tracking_number 查询号 运单号或者客户订单号
     * @param method_type 路由查询类别 1:标准路由查询 2:定制路由查询
     */
    public function RouteRequest($tracking_type, $tracking_number, $method_type = 1) {
        $RouteRequest = '<RouteRequest tracking_type="'.$tracking_type.'" tracking_number="'.$tracking_number.'" method_type="'.$method_type.'" />';
        $data = $this->postXmlBodyWithVerify($RouteRequest);
        echo "\n{$data}\n";
        return $this->getResponse($data, 'RouteResponse');
    }
}
