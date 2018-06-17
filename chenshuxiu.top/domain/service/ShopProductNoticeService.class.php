<?php

class ShopProductNoticeService
{
    public static function pushNoticesByShopProduct(ShopProduct $shopProduct) {
        $warning_cnt = $shopProduct->warning_cnt;
        if ($warning_cnt > 0 && $shopProduct->getLeft_cntOfReal() > $warning_cnt * 5) {
            $hour = date('H', time());
            if (8 <= $hour && $hour < 22) {
                $shopProductNotices = ShopProductNoticeDao::getListByShopProduct($shopProduct);
                foreach ($shopProductNotices as $shopProductNotice) {
                    self::pushNotice($shopProductNotice);
                }
            } else {
                Debug::warn("药物商品[{$shopProduct->title}]的入库数量大于警戒值的5倍，通知用户购买！");
            }
        }
    }

    public static function pushNotice(ShopProductNotice $shopProductNotice) {
        if ($shopProductNotice->isNotNotice()) {
            if ($shopProductNotice->canNotice()) {
                $shopProductNotice->notice();
            } else {
                $shopProductNotice->overtime();
            }
        }
    }

}
