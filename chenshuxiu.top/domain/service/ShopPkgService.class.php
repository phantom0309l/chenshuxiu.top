<?php

class ShopPkgService
{
    public static function createDefaultShopPkgAndItemsByShopOrder(ShopOrder $shopOrder) {
        //生成配送单
        $shopPkg = self::createByShopOrder($shopOrder);

        //生成配送单明细
        $shopPkgItems = self::createItemsByShopPkgShopOrder($shopPkg, $shopOrder);

        //如果有赠品生成赠品shoppkgitem
        $shopPkg->tryAddGift($shopPkgItems);

        //配送单是否需要推送erp设置
        $shopPkg->need_push_erpSet($shopPkgItems);
    }

    private static function createByShopOrder(ShopOrder $shopOrder) {
        $row = array();
        $row["wxuserid"] = $shopOrder->wxuserid;
        $row["userid"] = $shopOrder->userid;
        $row["patientid"] = $shopOrder->patientid;
        $row["shoporderid"] = $shopOrder->id;
        $row["express_price"] = $shopOrder->express_price;
        $row["express_company"] = self::getInitExpress_company($shopOrder);
        $row["status"] = $shopOrder->status;

        $shopPkg = ShopPkg::createByBiz($row);
        $shopPkg->fangcun_platform_no = $shopPkg->id;

        return $shopPkg;
    }

    private static function createItemsByShopPkgShopOrder(ShopPkg $shopPkg, ShopOrder $shopOrder) {
        $shopPkgItems = [];

        foreach ($shopOrder->getShopOrderItems() as $shopOrderItem) {
            $shopPkgItem = ShopPkgItemDao::getByShopPkgShopProduct($shopPkg, $shopOrderItem->shopproduct);
            if (false == $shopPkgItem instanceof ShopPkgItem) {
                $row = array();
                $row["shoppkgid"] = $shopPkg->id;
                $row["shopproductid"] = $shopOrderItem->shopproductid;
                $row["price"] = $shopOrderItem->price;
                $row["cnt"] = $shopOrderItem->cnt;

                $shopPkgItems[] = ShopPkgItem::createByBiz($row);
            } else {
                $shopPkgItems[] = $shopPkgItem;
            }
        }
        return $shopPkgItems;
    }

    public static function divide(ShopPkg $shopPkgOld, $shopPkgNum, $dataArr) {
        $shopOrder = $shopPkgOld->shoporder;
        DBC::requireTrue($shopOrder->isValid(),"该订单[shoporderid={$shopOrder->id}]无效，不可拆分配送单");

        // 检查数据
        self::checkData($shopPkgOld, $shopPkgNum, $dataArr);

        $shopPkgs = [];
        // 创建拆分的配送单
        for ($i = 0; $i < $shopPkgNum; $i++) {
            $shopPkgs[] = ShopPkgService::createByShopPkgOld($shopPkgOld);
        }

        // 计算运费
        $shopPkgs_db = $shopOrder->getShopPkgs();
        Debug::trace($shopPkgs_db);
        $shopPkgs_all = array_merge($shopPkgs, $shopPkgs_db);
        Debug::trace($shopPkgs_all);
        array_splice($shopPkgs_all, array_search($shopPkgOld, $shopPkgs_all), 1);
        Debug::trace($shopPkgs_all);
        self::reCalcuExpressPrice($shopOrder, $shopPkgs_all);

        //生成配送单明细
        self::createItemsByShopPkgsAndData($shopPkgOld, $shopPkgs, $dataArr);

        // 删除旧的配送单与配送单明细
        self::deleteShopPkg($shopPkgOld);
    }

    private static function checkData(ShopPkg $shopPkgOld, $shopPkgNum, $dataArr) {
        $shopProductCnts = [];
        // 检查拆分前后配送单明细的数量要保持一致
        foreach ($dataArr as $shopProductId => $nums) {
            $shopProduct = ShopProduct::getById($shopProductId);
            $shopPkgItem = ShopPkgItemDao::getByShopPkgShopProduct($shopPkgOld, $shopProduct);
            DBC::requireTrue(array_sum($nums) == $shopPkgItem->cnt, '拆分后的数量与拆分前的不一致！');

            for ($i = 0; $i < $shopPkgNum; $i++) {
                $shopProductCnts[$i] += $nums[$i];
            }
        }

        // 检查数据，不能有某一单全部填0的数据
        foreach ($shopProductCnts as $i => $shopProductCnt) {
            $j = $i + 1;
            DBC::requireTrue($shopProductCnt, "配送单{$j}的商品数目全部为0，请重新分配。");
        }
    }

    private static function createItemsByShopPkgsAndData($shopPkgOld, $shopPkgs, $dataArr) {
        foreach ($shopPkgs as $i => $shopPkg) {
            $shopPkgItems = [];
            foreach ($dataArr as $shopProductId => $nums) {
                $shopProduct = ShopProduct::getById($shopProductId);
                // 不是数字或者数量为0，直接跳过
                if (false == is_numeric($nums[$i]) || $nums[$i] < 1) {
                    continue;
                }
                // 创建配送单明细
                $shopPkgItemOld = ShopPkgItemDao::getByShopPkgShopProduct($shopPkgOld, $shopProduct);
                $row = array();
                $row["shoppkgid"] = $shopPkg->id;
                $row["shopproductid"] = $shopProductId;
                $row["price"] = $shopPkgItemOld->price;
                $row["cnt"] = $nums[$i];

                $shopPkgItem = ShopPkgItem::createByBiz($row);
                $shopPkgItems[] = $shopPkgItem;
            }
            //配送单是否需要推送erp设置
            $shopPkg->need_push_erpSet($shopPkgItems);
            //重新设置物流公司
            $shopPkg->express_company = self::getExpress_company($shopPkgItems);
        }
    }

    private static function createByShopPkgOld(ShopPkg $shopPkgOld) {
        $row = array();
        $row["wxuserid"] = $shopPkgOld->wxuserid;
        $row["userid"] = $shopPkgOld->userid;
        $row["patientid"] = $shopPkgOld->patientid;
        $row["shoporderid"] = $shopPkgOld->shoporderid;
        $row["status"] = $shopPkgOld->status;

        $shopPkg = ShopPkg::createByBiz($row);
        $shopPkg->fangcun_platform_no = $shopPkg->id;

        return $shopPkg;
    }

    public static function deleteShopPkg(ShopPkg $shopPkg) {
        $result = GuanYiService::tradeGetByShopPkg($shopPkg);
        Debug::trace("管易云查询接口返回：", $result);

        $success = $result["success"];
        DBC::requireTrue($success, "删除配送单时，查询erp接口返回失败！");

        $orders = $result["orders"];
        DBC::requireTrue(0 == count($orders), "您要删除的配送单，已经推送到了erp系统【shoppkgid={$shopPkg->id}】");

        // 没有在erp查询到信息，则可以删除
        self::deleteShopPkgItems($shopPkg);
        $shopPkg->remove();
    }

    private static function deleteShopPkgItems(ShopPkg $shopPkg) {
        $shopPkgItems = $shopPkg->getShopPkgItems();
        foreach ($shopPkgItems as $shopPkgItem) {
            $shopPkgItem->remove();
        }
    }

    public static function isContainWater($shopPkgItems) {
        foreach ($shopPkgItems as $shopPkgItem) {
            $shopProduct = $shopPkgItem->shopproduct;
            if ($shopProduct->isWater()) {
                return true;
            }
        }
        return false;
    }

    //获取初始快递公司
    public static function getInitExpress_company($shopOrder){
        $str = "顺丰";
        //因上合组织会议，6.8--6.12号发往青岛市的中通快递改为顺丰。
        $shopAddress = $shopOrder->shopaddress;
        $xcityid = $shopAddress->xcityid;

        if (time() < strtotime("2018-06-13") && time() > strtotime("2018-06-08") && 370200 == $xcityid) {
            return $str;
        }

        $isContainWater = $shopOrder->isContainWater();
        if($isContainWater){
            $str = "顺丰";
        }else{
            $str = "中通";
        }
        return $str;
    }

    //获取快递公司
    public static function getExpress_company($shopPkgItems){
        $str = "顺丰";
        //因上合组织会议，6.8--6.12号发往青岛市的中通快递改为顺丰。
        $shopPkgItem = $shopPkgItems[0];
        $shopAddress = $shopPkgItem->shoppkg->shoporder->shopaddress;
        $xcityid = $shopAddress->xcityid;

        if (time() < strtotime("2018-06-13") && time() > strtotime("2018-06-08") && 370200 == $xcityid) {
            return $str;
        }

        $isContainWater = self::isContainWater($shopPkgItems);
        if($isContainWater){
            $str = "顺丰";
        }else{
            $str = "中通";
        }
        return $str;
    }

    // 重新计算运费
    public static function reCalcuExpressPrice(ShopOrder $shopOrder, $shopPkgs) {
        $shopPkgExpress_price = number_format($shopOrder->express_price / count($shopPkgs));
        $shopPkgExpress_price_all = 0;
        foreach ($shopPkgs as $shopPkg) {
            $shopPkg->express_price = $shopPkgExpress_price;
            $shopPkgExpress_price_all += $shopPkgExpress_price;
        }

        if ($shopPkgExpress_price_all != $shopOrder->express_price) {
            $shopPkgs[0]->express_price += $shopOrder->express_price - $shopPkgExpress_price_all;
        }
    }
}
