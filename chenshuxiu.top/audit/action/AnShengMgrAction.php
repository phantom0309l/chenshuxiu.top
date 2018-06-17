<?php

class AnShengMgrAction extends AuditBaseAction
{

    private $shopProduct_jingling10_fix = 0;
    private $shopProduct_jingling10_gift_fix = 0;
    private $end_date = '2018-06-07';

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 1200);
        $pagenum = XRequest::getValue("pagenum", 1);

        $start_date = XRequest::getValue("start_date", date("Y-m-d", strtotime(date("Y-m-d")) - 6*86400 ));
        $end_date = XRequest::getValue("end_date", date("Y-m-d"));


        $type = XRequest::getValue("type", 1);
        $result = [];
        $bind = [];
        $cond = "";

        if($type == 1){
            $cond .= " ((b.shopproductid in (287697596, 543580646) and a.createtime < '2018-06-07') or (b.shopproductid in (287697596) and a.createtime >= '2018-06-07')) and a.createtime > '2018-05-01'";
            if ($start_date) {
                $cond .= " and a.createtime >= :start_date ";
                $bind[':start_date'] = $start_date;
            }

            if ($end_date) {
                $cond .= " and a.createtime < :end_date ";
                $end_date_fix = date("Y-m-d", strtotime($end_date) + 86400);
                $bind[':end_date'] = $end_date_fix;
            }

            //获得实体
            $sql = "select sum(a.cnt) as sumcnt,a.* from
                  shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                where  {$cond} group by a.shoporderitemid having sumcnt > 0 order by a.createtime asc";
            $shopOrderItemStockItemRefs = Dao::loadEntityList4Page("ShopOrderItemStockItemRef", $sql, $pagesize, $pagenum, $bind);

            foreach($shopOrderItemStockItemRefs as $a){

                if($this->needContinue($a)){
                    continue;
                }

                $shopOrderItem = $a->shoporderitem;
                $shopProduct = $shopOrderItem->shopproduct;

                $temp = [];
                //商品编码
                $temp["code"] = $this->getFixShopProductId($a);

                //商品名称
                //$temp["title"] = $shopProduct->title;
                $temp["title"] = $this->getFixShopProductTitle($a);

                //生产企业
                $temp["product_factory"] = "辽宁东方人药业有限公司";

                //日期
                $temp["date"] = substr($a->createtime, 0, 10);

                //类型
                $temp["type"] = "出库";

                $fixData = $this->getFixData($a);
                //机构名称
                //$xname = $shopOrderItem->shoporder->thedoctor->hospital->name;
                $temp["xname"] = $fixData[0];

                //医生姓名
                //$temp["doctor_name"] = mb_substr($shopOrderItem->shoporder->thedoctor->name, 0, 1) . "医生";
                $temp["doctor_name"] = $fixData[1];

                //第几次处方
                $temp["pos"] = $shopOrderItem->shoporder->pos;

                //入数量
                $temp["in_cnt"] = 0;

                //出数量
                $temp["out_cnt"] = $this->getFixOut_cnt($a);

                //批号
                $temp["batch_number"] = $a->stockitem->batch_number;

                $result[] = $temp;
            }

            // 翻页begin
            $countSql = "select count(*) as cnt from (
                select sum(a.cnt) as sumcnt,a.* from
                  shoporderitemstockitemrefs a
                inner join shoporderitems b on b.id = a.shoporderitemid
                where  {$cond} group by a.shoporderitemid having sumcnt > 0
            )t";

        }else{
            $cond .= "  shopproductid in (287697596, 543580646) and createtime > '2018-05-01'";

            if ($start_date) {
                $cond .= " and createtime >= :start_date ";
                $bind[':start_date'] = $start_date;
            }

            if ($end_date) {
                $cond .= " and createtime < :end_date ";
                $end_date_fix = date("Y-m-d", strtotime($end_date) + 86400);
                $bind[':end_date'] = $end_date_fix;
            }

            //获得实体
            $sql = "select * from
                  stockitems
                where  {$cond}";
            $stockItems = Dao::loadEntityList4Page("StockItem", $sql, $pagesize, $pagenum, $bind);
            foreach($stockItems as $a) {
                $shopProduct = $a->shopproduct;

                $temp = [];
                //商品编码
                $temp["code"] = $shopProduct->id;

                //商品名称
                $temp["title"] = $shopProduct->title;

                //生产企业
                $temp["product_factory"] = "辽宁东方人药业有限公司";

                //医生姓名
                $temp["doctor_name"] = "";

                //第几次处方
                $temp["pos"] = "";

                //日期
                $temp["date"] = substr($a->createtime, 0, 10);

                //类型
                $temp["type"] = "入库";

                //机构名称
                $xname = $a->sourse;
                $temp["xname"] = $xname;

                //入数量
                $temp["in_cnt"] = $a->cnt;

                //出数量
                $temp["out_cnt"] = 0;

                //批号
                $temp["batch_number"] = $a->batch_number;

                $result[] = $temp;
            }

            // 翻页begin
            $countSql = "select count(*) from
                  stockitems
                where  {$cond}";
        }

        $shopProduct_jingling10 = ShopProduct::getById(ShopProduct::JINGLING10_ID);
        $shopProduct_jingling10_gift = ShopProduct::getById(ShopProduct::JINGLING10_GIFT_ID);
        $jingling10_left_cnt = $shopProduct_jingling10->left_cnt + $this->shopProduct_jingling10_fix;
        $jingling10_gift_left_cnt = $shopProduct_jingling10_gift->left_cnt + $this->shopProduct_jingling10_gift_fix;

        XContext::setValue('start_date', $start_date);
        XContext::setValue('end_date', $end_date);
        XContext::setValue('type', $type);
        XContext::setValue('result', $result);

        XContext::setValue('jingling10_left_cnt', $jingling10_left_cnt);
        XContext::setValue('jingling10_gift_left_cnt', $jingling10_gift_left_cnt);

        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/anshengmgr/list/?start_date={$start_date}&end_date={$end_date}&type={$type}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url, $bind);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    private function getFixShopProductId($shopOrderItemStockItemRef){
        $end_date = $this->end_date;

        $createtime = $shopOrderItemStockItemRef->createtime;
        $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;
        $shopProduct = $shopOrderItem->shopproduct;
        if($createtime < $end_date){
            return $shopProduct->id;
        }else{
            if($shopProduct->isGift()){
                return "287697596";
            }else{
                return $shopProduct->id;
            }
        }
    }

    private function getFixShopProductTitle($shopOrderItemStockItemRef){
        $end_date = $this->end_date;
        $createtime = $shopOrderItemStockItemRef->createtime;
        $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;
        $shopProduct = $shopOrderItem->shopproduct;
        if($createtime < $end_date){
            return $shopProduct->title;
        }else{
            if($shopProduct->isGift()){
                return "静灵口服液(安生),10ml*10支";
            }else{
                return $shopProduct->title;
            }
        }
    }

    private function getFixOut_cnt($shopOrderItemStockItemRef){
        $end_date = $this->end_date;
        $createtime = $shopOrderItemStockItemRef->createtime;
        $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;

        $cnt1 = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopOrderItem($shopOrderItem);
        if($createtime < $end_date){
            return $cnt1;
        }

        $shopOrder = $shopOrderItem->shoporder;
        $shopProductid = "543580646";
        $shopProduct = ShopProduct::getById($shopProductid);
        $cnt2 = 0;
        $gift_item = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopOrder,$shopProduct);
        if($gift_item instanceof ShopOrderItem){
            $cnt2 = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopOrderItem($gift_item);
        }
        return $cnt1 + $cnt2;
    }

    private function needContinue($shopOrderItemStockItemRef){
        $createtime = $shopOrderItemStockItemRef->createtime;
        $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;
        $hospitalid = $shopOrderItem->shoporder->thedoctor->hospitalid;
        $begin_date = "2018-06-07";
        $end_date = "2018-06-15";
        $hospitalids = [47,71,22,95,9,13,48,235,164];

        if($createtime < $end_date && $createtime > $begin_date && in_array($hospitalid, $hospitalids)){
            $shopProduct = $shopOrderItem->shopproduct;
            $cnt = ShopOrderItemStockItemRefDao::getHasGoodsOutCntByShopOrderItem($shopOrderItem);
            if($shopProduct->isGift()){
                $this->shopProduct_jingling10_gift_fix += $cnt;
            }else{
                $this->shopProduct_jingling10_fix += $cnt;
            }
            return true;
        }else{
            return false;
        }
    }

    private function getFixData($shopOrderItemStockItemRef){
        $end_date = $this->end_date;

        $createtime = $shopOrderItemStockItemRef->createtime;
        $shopOrderItem = $shopOrderItemStockItemRef->shoporderitem;
        $hospital = $shopOrderItem->shoporder->thedoctor->hospital;

        $xname = $hospital->name;
        $doctor_name = mb_substr($shopOrderItem->shoporder->thedoctor->name, 0, 1) . "医生";

        if($createtime < $end_date){
            return [$xname, $doctor_name];
        }

        if(in_array($hospital->id, array(106,23,132,214,26,196,218,583))){
            return [$xname, $doctor_name];
        }

        $id = $shopOrderItem->id;
        $id_part = substr($id,-3,1);
        $config = $this->getFixConfig($id_part);
        if(empty($config)){
            return ["", ""];
        }
        $xname = $config["fix_name"];
        $doctor_name = $config["doctor_name"];
        return [$xname, $doctor_name];
    }

    private function getFixConfig($no){
        $arr = [
            0 => [
                "id" => 106,
                "fix_name" => "广东省妇幼保健院",
                "doctor_name" => "罗医生",
            ],
            1 => [
                "id" => 106,
                "fix_name" => "广东省妇幼保健院",
                "doctor_name" => "黄医生",
            ],
            2 => [
                "id" => 23,
                "fix_name" => "广东省人民医院",
                "doctor_name" => "王医生",
            ],
            3 => [
                "id" => 23,
                "fix_name" => "广东省人民医院",
                "doctor_name" => "梁医生",
            ],
            4 => [
                "id" => 132,
                "fix_name" => "暨南大学附属第一医院",
                "doctor_name" => "贾医生",
            ],
            5 => [
                "id" => 214,
                "fix_name" => "惠州市第一妇幼保健院",
                "doctor_name" => "张医生",
            ],
            6 => [
                "id" => 26,
                "fix_name" => "广州医科大学附属第二医院",
                "doctor_name" => "刘医生",
            ],
            7 => [
                "id" => 196,
                "fix_name" => "佛山市妇幼保健院",
                "doctor_name" => "黄医生",
            ],
            8 => [
                "id" => 218,
                "fix_name" => "江门市第三人民医院",
                "doctor_name" => "朱医生",
            ],
            9 => [
                "id" => 583,
                "fix_name" => "中山市博爱医院",
                "doctor_name" => "何医生",
            ],
        ];

        return $arr[$no];
    }

    private function getFixConfig_bak($hospital_name){
        $arr = [
            "吉林大学白求恩第一医院" => [
                "trigger" => [1,2,3],
                "fix_name" => "广东省妇幼保健院",
                1 => "张医生1",
                2 => "李医生1",
                3 => "王医生1",
            ],
            "青岛市妇女儿童医院" => [
                "trigger" => [1,2],
                "fix_name" => "广东省人民医院",
                1 => "张医生2",
                2 => "王医生2",
            ],
            "青岛市妇女儿童医院" => [
                "trigger" => [3],
                "fix_name" => "暨南大学附属第一医院",
                3 => "王医生3",
            ],
            "长春市儿童医院" => [
                "trigger" => [1,2,3],
                "fix_name" => "惠州市第一妇幼保健院",
                1 => "王医生3",
                2 => "王医生3",
                3 => "王医生3",
            ],
        ];

        return $arr[$hospital_name];
    }

}
