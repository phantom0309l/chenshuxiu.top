<?php

class BSPOrderService extends BaseBSP
{

    /**
     * 下订单（含筛选）
     * 下订单接口根据客户需要，可提供以下三个功能：
     * 1) 客户系统向顺丰下发订单。
     * 2) 为订单分配运单号。
     * 3) 筛单（可选，具体商务沟通中双方约定，由顺丰内部为客户配置）。
     * 此接口也用于路由推送注册。客户的顺丰运单号不是通过此下订单接口获取，但却需要获取BSP的路由推送时，
     * 需要通过此接口对相应的顺丰运单进行注册以使用BSP的路由推送 接口。
     *
     * @param string $orderid //客户订单号
     * @param string $d_company //到件名称
     * @param string $d_contact //到件方联系人
     * @param string $d_tel //到件方联系电话
     * @param string $d_address //到件方详细地址，如果不传输 d_province/d_city 字段，此详细地址 需包含省市信息，以提高地址识别的 成功率，示例：“广东省深圳市福田 区新洲十一街万基商务大厦 10楼”。
     * @param array $params //可选参数的数组
     * @param array $cargoes
     * @param array $addedServices
     * @return string
     */
    public function Order($orderid, $d_contact, $d_tel, $d_address, $params = array(), $addedServices = array(), $cargoes = array())
    {
        $params['orderid'] = $orderid;
        $params['d_contact'] = $d_contact;
        $params['d_tel'] = $d_tel;
        $params['d_address'] = $d_address;

        $order_params = $this->paramsToString($params);
        $addedServices_str = count($addedServices) > 0 ? $this->paramsToString($addedServices, 'AddedService') : '';
        $cargoes_str = count($cargoes) > 0 ? $this->paramsToString($cargoes, 'Cargo') : '';
        $xml_string = "<Order$order_params>$cargoes_str$addedServices_str</Order>";
        $data = $this->postXmlBodyWithVerify($xml_string);
        return $this->OrderResponse($data);
    }

    /**
     * 返回结果
     * @param $data
     * @return array
     */
    private function  OrderResponse($data) {
        return $this->getResponse($data, 'OrderResponse');
    }
}
