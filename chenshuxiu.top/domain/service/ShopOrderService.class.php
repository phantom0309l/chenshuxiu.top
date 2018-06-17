<?php

class ShopOrderService
{
    public static function updateItems(ShopOrder $shopOrder) {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach ($shopOrderItems as $shopOrderItem) {
            $shopProduct = $shopOrderItem->shopproduct;
            if ($shopOrderItem->isLack()) {
                $shopOrderItem_lack = ShopOrderItem_lackDao::getByShopOrderShopProduct($shopOrder, $shopProduct);
                if (false == $shopOrderItem_lack instanceof ShopOrderItem_lack) {
                    $row = array();
                    $row["shoporderid"] = $shopOrderItem->shoporderid;
                    $row["shopproductid"] = $shopOrderItem->shopproductid;
                    $row["price"] = $shopOrderItem->shopproduct->price;
                    $row["cnt"] = $shopOrderItem->cnt;
                    ShopOrderItem_lack::createByBiz($row);
                } else {
                    Debug::warn("合并shoporderitem[{$shopOrderItem->id}]至shoporderitem_lack[{$shopOrderItem_lack->id}]");
                    $shopOrderItem_lack->price = $shopProduct->price;
                    $shopOrderItem_lack->cnt = $shopOrderItem->cnt;
                }
                $shopOrderItem->remove();
            }
        }
        $shopOrderItem_lacks = $shopOrder->getShopOrderItem_lacks();
        foreach ($shopOrderItem_lacks as $shopOrderItem_lack) {
            $shopProduct = $shopOrderItem_lack->shopproduct;
            if ($shopOrderItem_lack->isNotLack()) {
                $shopOrderItem = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopOrder, $shopProduct);
                if (false == $shopOrderItem instanceof ShopOrderItem) {
                    $row = array();
                    $row["shoporderid"] = $shopOrderItem_lack->shoporderid;
                    $row["shopproductid"] = $shopOrderItem_lack->shopproductid;
                    $row["price"] = $shopOrderItem_lack->shopproduct->price;
                    $row["cnt"] = $shopOrderItem_lack->cnt;
                    ShopOrderItem::createByBiz($row);
                } else {
                    Debug::warn("合并shoporderitem_lack[{$shopOrderItem_lack->id}]至shoporderitem[{$shopOrderItem->id}]");
                    $shopOrderItem->price = $shopProduct->price;
                    $shopOrderItem->cnt = $shopOrderItem_lack->cnt;
                }
                $shopOrderItem_lack->remove();
            }
        }
        $unitofwork->commitAndInit();
    }

    public static function needUpdateItems(ShopOrder $shopOrder) {
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach ($shopOrderItems as $shopOrderItem) {
            if ($shopOrderItem->isLack()) {
                return true;
            }
        }
        $shopOrderItem_lacks = $shopOrder->getShopOrderItem_lacks();
        foreach ($shopOrderItem_lacks as $shopOrderItem_lack) {
            if ($shopOrderItem_lack->isNotLack()) {
                return true;
            }
        }
        return false;
    }

    public static function removeAndInitItems(ShopOrder $shopOrder, $shopproductids) {
        $shopOrderItem_lacks = $shopOrder->getShopOrderItem_lacks();
        foreach ($shopOrderItem_lacks as $a) {
            $a->remove();
        }
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach ($shopOrderItems as $a) {
            $a->remove();
        }
        foreach ($shopproductids as $shopproductid) {
            $shopProduct = ShopProduct::getById($shopproductid);
            $shopOrderItem = self::addOrModifyShopOrderItem($shopOrder, $shopProduct);
            $shopOrderItem->unRemove();
        }
    }

    private static function addOrModifyShopOrderItem(ShopOrder $shopOrder, ShopProduct $shopProduct) {
        $shopOrderItem = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopOrder, $shopProduct);
        //初始值
        $buy_cnt_init = $shopProduct->buy_cnt_init;
        if ($shopOrderItem instanceof ShopOrderItem) {
            $shopOrderItem->cnt = $buy_cnt_init;
        } else {
            $price = $shopProduct->price;
            DBC::requireTrue($price > 0, "shopProduct[{$shopProduct->id}]价格必须大于0");
            $row = array();
            $row["shoporderid"] = $shopOrder->id;
            $row["shopproductid"] = $shopProduct->id;
            $row["price"] = $price;
            $row["cnt"] = $buy_cnt_init;
            $shopOrderItem = ShopOrderItem::createByBiz($row);
        }
        return $shopOrderItem;
    }

    // 出库时检验shoporder的商品数量要与所有配送单shoppkg里的的商品数量一致
    public static function isBalance(ShopOrder $shopOrder) {
        $shopOrderItems = ShopOrderItemDao::getShopOrderItemsByShopOrder($shopOrder);
        foreach ($shopOrderItems as $shopOrderItem) {
            $cnt = ShopPkgItemDao::getCntByShopOrderShopProduct($shopOrder, $shopOrderItem->shopproduct);
//            DBC::requireTrue($cnt == $shopOrderItem->cnt, "订单【shoporderid{$shopOrder->id}】的商品【shopproductid{$shopOrderItem->shopproductid}】没有完全拆分到配送单！！！");
            if($cnt != $shopOrderItem->cnt){
                return false;
            }
        }
        return true;
    }
}
