<?php

class ShopProductService
{
    // 退货
    public static function goodsBack (ShopOrderItem $shopOrderItem, $goods_back_cnt, $remark = "", $is_recycle = 0) {
        $shopOrder = $shopOrderItem->shoporder;

        //无效订单不能退货
        if(false == $shopOrder->isValid()){
            return false;
        }

        //未出库不能退货
        if(!$shopOrder->isGoodsOutAll()){
            return false;
        }

        //特定时间之前不能够退货
        $time_pay = $shopOrder->time_pay;
        if($time_pay < "2017-08-26 13:00:00"){
            return false;
        }

        //当前最大退货数
        $max_goods_back_cnt = $shopOrderItem->getMaxGoodsBackCnt();
        if($max_goods_back_cnt <= 0){
            return false;
        }

        //退货数大于最大退货数
        if($goods_back_cnt > $max_goods_back_cnt){
            $goods_back_cnt = $max_goods_back_cnt;
        }

        //备注
        $shopOrder->remark .= "{$remark}\n";

        //退钱，vip会员是9.5折，不要多退
        /*$ratio = 1;
        if($shopOrder->patient->isMenZhenVip()){
            $ratio = 0.95;
        }
        $refund_amount = $shopOrderItem->price * $goods_back_cnt * $ratio;
        DBC::requireTrue($refund_amount > 0, "退款额度不能为0");
        $shopOrder->refund($refund_amount, "{$shopOrderItem->shopproduct->title}退货,退货数：{$goods_back_cnt}");*/

        $arr = array();
        $shopOrderItemStockItemRefs = ShopOrderItemStockItemRefDao::getListByShopOrderItem($shopOrderItem);
        foreach($shopOrderItemStockItemRefs as $shopOrderItemStockItemRef){
            $stockitemid = $shopOrderItemStockItemRef->stockitemid;
            $cnt = $shopOrderItemStockItemRef->cnt;

            if($arr[$stockitemid]){
                $arr[$stockitemid] += $cnt;
            }else{
                $arr[$stockitemid] = $cnt;
            }
        }

        foreach($arr as $stockitemid => $cnt){
            if($cnt <= 0){
                continue;
            }

            //退完了不用退了
            if($goods_back_cnt < 1){
                break;
            }

            if($cnt >= $goods_back_cnt){
                $t = $goods_back_cnt;
            }else{
                $t = $cnt;
            }

            $stockItem = StockItem::getById($stockitemid);
            $stockItem_left_cnt = $stockItem->left_cnt;
            $stockItem_cnt = $stockItem->cnt;
            if($stockItem_left_cnt + $t > $stockItem_cnt){
                Debug::warn("退货后剩余数量大于入库时的数量stockitemid[{$stockitemid}]");
                //return false;
            }

            $row = array();
            $row["shoporderitemid"] = $shopOrderItem->id;
            $row["stockitemid"] = $stockitemid;
            $row["cnt"] = 0 - $t;
            $row["is_recycle"] = $is_recycle;
            ShopOrderItemStockItemRef::createByBiz($row);
            //需要回库存情况
            if($is_recycle){
                //本次入库单剩余数量增加
                $stockItem->left_cnt += $t;
                //库存数增加
                $shopOrderItem->shopproduct->left_cnt += $t;
            }

            $goods_back_cnt = $goods_back_cnt - $t;
        }
        return true;
    }

    // 出货
    public static function goodsOut (ShopPkgItem $shopPkgItem) {
        $shopOrder = $shopPkgItem->shoppkg->shoporder;
        $goods_out_cnt = $shopPkgItem->cnt;

        $shopproduct = $shopPkgItem->shopproduct;
        $shopproduct_left_cnt = $shopproduct->left_cnt;

        $shopOrderItem = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopOrder, $shopproduct);
        DBC::requireTrue($shopOrderItem instanceof ShopOrderItem, "出库时未发现订单【shoporderid:{$shopOrder->id}】的商品【shopproductid:{$shopproduct->id}】明细。");

        if ($goods_out_cnt < 1) {
            return false;
        }

        //出货量大于了库存
        if($goods_out_cnt > $shopproduct_left_cnt){
            return false;
        }

        $stockItems = StockItemDao::getListForGoodsOutByShopProductAndCnt($shopproduct, $goods_out_cnt);
        foreach($stockItems as $stockItem){

            if ($goods_out_cnt < 1) {
                break;
            }

            $left_cnt = $stockItem->left_cnt;
            if($left_cnt >= $goods_out_cnt){
                $t = $goods_out_cnt;
            }else{
                $t = $left_cnt;
            }

            $row = array();
            $row["shoporderitemid"] = $shopOrderItem->id;
            $row["stockitemid"] = $stockItem->id;
            $row["cnt"] = $t;
            $row["is_recycle"] = 0;
            ShopOrderItemStockItemRef::createByBiz($row);

            $goods_out_cnt = $goods_out_cnt - $t;
            $stockItem->left_cnt -= $t;
            $shopproduct->left_cnt -= $t;
        }

        $shopproduct_left_cnt = $shopproduct->left_cnt;
        $shopproduct_notice_cnt = $shopproduct->notice_cnt;
        if($shopproduct_left_cnt <= $shopproduct_notice_cnt){
            $content = "[{$shopproduct->title}]剩余[{$shopproduct_left_cnt}]请及时补货！";
            PushMsgService::sendMsgToAuditorBySystem('ShopOrder', 1, $content);
        }
        return true;
    }
}
