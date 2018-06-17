<?php

// 创建: 20170717 by txj
class FreightService
{
    // 获取默认运费
    public static function getDefaultFreight () {
        return 1000;
    }

    public static function getFreight_yuan () {
        $n = self::getDefaultFreight();
        return $n/100;
    }

    //获取运费,基于shoporder
    public static function getFreight (ShopOrder $shopOrder) {
        $amount = 0;
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach($shopOrderItems as $a){
            $shopProduct = $a->shopproduct;
            $shopProductid = $a->shopproductid;
            $cnt = $a->cnt;

            //静灵口服液(安生),10ml*24支
            if($shopProduct->isJingLing24()){
                $gift_cnt = $shopProduct->canCreateGiftCnt($cnt);
                $cnt = $cnt + $gift_cnt;

                $amount_temp = 0;
                if($cnt <= 4){
                    $amount_temp = 1000;
                }

                if($cnt > 4 && $cnt <= 8){
                    $amount_temp = 2000;
                }

                if($cnt > 8){
                    $amount_temp = 3000;
                }

                $amount = ($amount < $amount_temp) ? $amount_temp : $amount;
                continue;
            }

            //静灵口服液(安生),10ml*10支
            if($shopProduct->isJingLing10()){
                $gift_cnt = $shopProduct->canCreateGiftCnt($cnt);
                $cnt = $cnt + $gift_cnt;

                $amount_temp = 0;
                if($cnt <= 4){
                    $amount_temp = 1000;
                }

                if($cnt > 4 && $cnt < 9){
                    $amount_temp = 2000;
                }

                //买5盒赠1盒免运费
                if($cnt >= 6){
                    $amount = 0;
                    return $amount;
                }

                $amount = ($amount < $amount_temp) ? $amount_temp : $amount;
                continue;
            }

            //脑蛋白水解物口服液
            $ids1 = [287700186];
            if( in_array($shopProductid, $ids1) ){
                $gift_cnt = $shopProduct->canCreateGiftCnt($cnt);
                $cnt = $cnt + $gift_cnt;

                $amount_temp = 0;
                if($cnt <= 6){
                    $amount_temp = 1000;
                }

                if($cnt > 6 && $cnt <= 12){
                    $amount_temp = 2000;
                }

                if($cnt > 12){
                    $amount_temp = 3000;
                }

                $amount = ($amount < $amount_temp) ? $amount_temp : $amount;
                continue;
            }

            //地牡宁神口服液10ml*6支、地牡宁神口服液10ml*10支、小儿智力糖浆、迪而慧聪、信福爱、利培酮口服液、生血宝合剂
            $ids2 = [638615366, 287696276, 282709756, 287695876, 305404166, 315866086, 315873746, 467658196, 551891336];
            if( in_array($shopProductid, $ids2) ){

                $amount_temp = 0;
                if($cnt <= 10){
                    $amount_temp = 1000;
                }

                if($cnt > 10 && $cnt <= 20){
                    $amount_temp = 2000;
                }

                if($cnt > 20){
                    $amount_temp = 3000;
                }

                $amount = ($amount < $amount_temp) ? $amount_temp : $amount;
                continue;
            }

            //乳酸亚铁口服液、鸦胆子油口服乳液、榄香烯口服乳、生白口服液、转移因子口服溶液、金复康口服液
            $ids3 = [507203976, 546491746, 551920346, 554606816, 504023516, 507253516, 570803926];
            if( in_array($shopProductid, $ids3) ){

                $amount_temp = 0;
                if($cnt <= 5){
                    $amount_temp = 1000;
                }

                if($cnt > 5 && $cnt <= 10){
                    $amount_temp = 2000;
                }

                if($cnt > 10){
                    $amount_temp = 3000;
                }

                $amount = ($amount < $amount_temp) ? $amount_temp : $amount;
                continue;
            }

        }

        if($amount == 0){
            $amount = self::getDefaultFreight();
        }
        return $amount;
    }

}
