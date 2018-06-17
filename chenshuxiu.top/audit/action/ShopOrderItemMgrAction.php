<?php

// ShopOrderItemMgrAction
class ShopOrderItemMgrAction extends AuditBaseAction
{

    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);
        $patientid = XRequest::getValue('patientid', 0);
        $shopproductid = XRequest::getValue('shopproductid', 0);
        $pay = XRequest::getValue('pay', 'all');

        $cond = '';
        $bind = [];

        // 订单是否支付
        if ($pay != 'all') {
            $is_pay = ($pay == 'pay') ? 1 : 0;

            $cond .= " and b.is_pay = :is_pay ";
            $bind[':is_pay'] = $is_pay;
        }

        // 按患者筛选
        if ($patientid > 0) {
            $cond .= " and b.patientid = :patientid ";
            $bind[':patientid'] = $patientid;

            $patient = Patient::getById($patientid);
            XContext::setValue("patient", $patient);
        }

        // 按商品筛选
        if ($shopproductid > 0) {
            $cond .= " and a.shopproductid = :shopproductid ";
            $bind[':shopproductid'] = $shopproductid;

            $shopProduct = ShopProduct::getById($shopproductid);
            XContext::setValue("shopProduct", $shopProduct);
        }

        $sql = "select a.*
                    from shoporderitems a
                    inner join shoporders b on b.id = a.shoporderid
                    where 1=1 {$cond} order by a.id desc";
        $shopOrderItems = Dao::loadEntityList4Page("ShopOrderItem", $sql, $pagesize, $pagenum, $bind);

        XContext::setValue('pay', $pay);
        XContext::setValue('shopOrderItems', $shopOrderItems);

        //获得分页
        $countSql = "select count(a.id)
                        from shoporderitems a
                        inner join shoporders b on b.id = a.shoporderid
                        where 1=1 {$cond} order by a.id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shoporderitemmgr/list?pay={$pay}&patientid={$patientid}&pay={$pay}&shopproductid={$shopproductid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doRefundShopProduct() {
        $shoporderitemid = XRequest::getValue('shoporderitemid', 0);
        $is_recycle = XRequest::getValue('is_recycle', 0);

        $shopOrderItem = ShopOrderItem::getById($shoporderitemid);
        DBC::requireTrue($shopOrderItem instanceof ShopOrderItem, "shoporderitem不存在:{$shoporderitemid}");
        XContext::setValue('is_recycle', $is_recycle);
        XContext::setValue('shopOrderItem', $shopOrderItem);
        return self::SUCCESS;
    }

    public function doRefundShopProductPost() {
        $shoporderitemid = XRequest::getValue('shoporderitemid', 0);
        $goods_back_cnt = XRequest::getValue('goods_back_cnt', 0);
        DBC::requireTrue($goods_back_cnt > 0, "退货数需大于0");

        $remark = XRequest::getValue('remark', '');
        $is_recycle = XRequest::getValue('is_recycle', 0);
        $shopOrderItem = ShopOrderItem::getById($shoporderitemid);
        DBC::requireTrue($shopOrderItem instanceof ShopOrderItem, "shoporderitem不存在:{$shoporderitemid}");

        $is_goodsback = ShopProductService::goodsBack($shopOrderItem, $goods_back_cnt, $remark, $is_recycle);

        $preMsg = "退货失败 " . XDateTime::now();
        if ($is_goodsback) {
            $preMsg = "退货完成" . XDateTime::now();
        }

        $shopOrder = $shopOrderItem->shoporder;
        $url = "/shopordermgr/one?shoporderid={$shopOrder->id}&preMsg=" . urlencode($preMsg);
        XContext::setJumpPath($url);

        return self::SUCCESS;
    }
}
