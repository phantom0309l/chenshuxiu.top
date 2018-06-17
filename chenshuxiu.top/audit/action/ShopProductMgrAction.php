<?php

// ShopProductMgrAction
class ShopProductMgrAction extends AuditBaseAction
{

    public function doList () {
        $diseasegroupid = XRequest::getValue('diseasegroupid', 2);
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $status = XRequest::getValue('status', 2);
        $medicine_type = XRequest::getValue('medicine_type', 'all');
        $notice_line = XRequest::getValue('notice_line', 'all');

        // 初始化商品类型
        $shopProductTypes = [];

        // 初始化商品
        $shopProducts = [];
        if ($diseasegroupid) {
            $shopProductTypes = ShopProductTypeDao::getListByDiseaseGroupid($diseasegroupid);
        }

        $cond = "";
        $bind = [];

        if ($diseasegroupid) {
            $cond .= " and b.diseasegroupid=:diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        if ($shopproducttypeid > 0) {
            $cond .= " and a.shopproducttypeid=:shopproducttypeid ";
            $bind[":shopproducttypeid"] = $shopproducttypeid;
        }

        if ($status < 2) {
            $cond .= " and a.status=:status ";
            $bind[":status"] = $status;
        }

        if($medicine_type == "yes"){
            $cond .= " and a.objid > 0 and a.objtype = 'MedicineProduct' ";
        }

        if($medicine_type == "no"){
            $cond .= " and a.objid = 0 and a.objtype = '' ";
        }

        if($notice_line == "gt"){
            $cond .= " and a.left_cnt > a.notice_cnt ";
        }

        if($notice_line == "lt"){
            $cond .= " and a.left_cnt <= a.notice_cnt ";
        }

        $sql = "select a.*
            from shopproducts a
            inner join shopproducttypes b on b.id = a.shopproducttypeid
            where 1=1 " . $cond . " order by a.shopproducttypeid , a.pos, a.title_pinyin ";
        $shopProducts = Dao::loadEntityList("ShopProduct", $sql, $bind);

        XContext::setValue('shopproducttypeid', $shopproducttypeid);
        XContext::setValue('status', $status);

        XContext::setValue('shopProductTypes', $shopProductTypes);
        XContext::setValue('shopProducts', $shopProducts);
        XContext::setValue('diseasegroupid', $diseasegroupid);
        XContext::setValue('medicine_type', $medicine_type);
        XContext::setValue('notice_line', $notice_line);

        return self::SUCCESS;
    }

    public function doOne () {
        $shopproductid = XRequest::getValue('shopproductid', 0);

        $shopProduct = ShopProduct::getById($shopproductid);

        $shopProduct->resetTitle_pinyin();

        XContext::setValue('shopProduct', $shopProduct);

        return self::SUCCESS;
    }

    public function doAdd () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $mydisease = $this->mydisease;

        $obj = Dao::getEntityById($objtype, $objid);

        if ($obj instanceof Entity) {
            $shopProduct = ShopProductDao::getShopProductByObjtypeObjid($objtype, $objid);
            if ($shopProduct instanceof ShopProduct) {
                XContext::setJumpPath("/ShopProductMgr/one?shopproductid={$shopProduct->id}");
                return self::SUCCESS;
            }
        }

        // 拿到商品类型
        $shopProductTypes = [];
        if ($mydisease instanceof Disease && $mydisease->diseasegroup instanceof DiseaseGroup) {
            $shopProductTypes = ShopProductTypeDao::getListByDiseaseGroupid($mydisease->diseasegroupid);
        }

        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);
        XContext::setValue('obj', $obj);

        XContext::setValue('shopProductTypes', $shopProductTypes);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $sku_code = XRequest::getValue('sku_code', '');
        //DBC::requireNotEmpty($sku_code, 'sku_code 不能为空');
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $pictureid = XRequest::getValue('pictureid', 0);
        $title = XRequest::getValue('title', '');
        $product_factory = XRequest::getValue('product_factory', '');
        $is_water = XRequest::getValue('is_water', 0);
        $content = XRequest::getValue('content', '');
        $price_yuan = XRequest::getValue('price_yuan', 0);
        $market_price_yuan = XRequest::getValue('market_price_yuan', 0);
        $pack_unit = XRequest::getValue('pack_unit', '');
        $notice_cnt = XRequest::getValue('notice_cnt', 0);
        $warning_cnt = XRequest::getValue('warning_cnt', 0);

        DBC::requireNotEmpty($shopproducttypeid, 'shopproducttypeid is null');

        $row = array();
        $row["shopproducttypeid"] = $shopproducttypeid;
        $row["sku_code"] = $sku_code;
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["pictureid"] = $pictureid;
        $row["title"] = $title;
        $row["product_factory"] = $product_factory;
        $row["is_water"] = $is_water;
        $row["content"] = $content;
        $row["price"] = $price_yuan * 100;
        $row["market_price"] = $market_price_yuan * 100;
        $row["pack_unit"] = $pack_unit;
        $row["notice_cnt"] = $notice_cnt;
        $row["warning_cnt"] = $warning_cnt;
        $row["pos"] = Dao::queryValue("select max(pos) from shopproducts") + 1;
        $shopProduct = ShopProduct::createByBiz($row);

        XContext::setJumpPath("/ShopProductMgr/one?shopproductid={$shopProduct->id}");
        return self::SUCCESS;
    }

    public function doModify () {
        $shopproductid = XRequest::getValue('shopproductid', 0);
        DBC::requireNotEmpty($shopproductid, 'shopproductid is null');

        $mydisease = $this->mydisease;
        // 拿到商品类型
        $shopProductTypes = [];
        if ($mydisease instanceof Disease && $mydisease->diseasegroup instanceof DiseaseGroup) {
            $shopProductTypes = ShopProductTypeDao::getListByDiseaseGroupid($mydisease->diseasegroupid);
        }

        $shopProduct = ShopProduct::getById($shopproductid);

        XContext::setValue('shopProduct', $shopProduct);

        XContext::setValue('shopProductTypes', $shopProductTypes);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $shopproductid = XRequest::getValue('shopproductid', 0);
        DBC::requireNotEmpty($shopproductid, 'shopproductid is null');

        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $sku_code = XRequest::getValue('sku_code', '');
        $pictureid = XRequest::getValue('pictureid', 0);
        $title = XRequest::getValue('title', '');
        $product_factory = XRequest::getValue('product_factory', '');
        $is_water = XRequest::getValue('is_water', 0);
        $content = XRequest::getUnSafeValue('content', '');
        $price_yuan = XRequest::getValue('price_yuan', 0);
        $market_price_yuan = XRequest::getValue('market_price_yuan', 0);
        $pack_unit = XRequest::getValue('pack_unit', '');
        $notice_cnt = XRequest::getValue('notice_cnt', 5);
        $warning_cnt = XRequest::getValue('warning_cnt', 0);
        $buy_cnt_init = XRequest::getValue('buy_cnt_init', 4);
        $buy_cnt_max = XRequest::getValue('buy_cnt_max', 12);
        $status = XRequest::getValue('status', 0);
        $service_percent = XRequest::getValue('service_percent', 0);

        DBC::requireNotEmpty($shopproducttypeid, 'shopproducttypeid is null');

        $shopProduct = ShopProduct::getById($shopproductid);
        $shopProduct->shopproducttypeid = $shopproducttypeid;
        $shopProduct->sku_code = $sku_code;
        $shopProduct->pictureid = $pictureid;
        $shopProduct->title = $title;
        $shopProduct->product_factory = $product_factory;
        $shopProduct->is_water = $is_water;
        $shopProduct->content = $content;
        $shopProduct->price = $price_yuan * 100;
        $shopProduct->market_price = $market_price_yuan * 100;
        $shopProduct->pack_unit = $pack_unit;
        $shopProduct->notice_cnt = $notice_cnt;
        $shopProduct->warning_cnt = $warning_cnt;
        $shopProduct->buy_cnt_init = $buy_cnt_init;
        $shopProduct->buy_cnt_max = $buy_cnt_max;
        $shopProduct->service_percent = $service_percent;
        $shopProduct->status = $status;

        $shopProduct->resetTitle_pinyin();

        XContext::setJumpPath("/ShopProductMgr/one?shopproductid={$shopProduct->id}");
        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $shopproductid => $pos) {
            $entity = ShopProduct::getById($shopproductid);
            $entity->pos = $pos;
        }

        XContext::setJumpPath("/ShopProductMgr/list");
        return self::SUCCESS;
    }

    public function doListForSumPrice () {
        $diseasegroupid = XRequest::getValue('diseasegroupid', 2);
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $status = XRequest::getValue('status', 2);
        $medicine_type = XRequest::getValue('medicine_type', 'all');

        //时间维度
        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 6 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        // 初始化商品类型
        $shopProductTypes = [];

        // 初始化商品
        $shopProducts = [];
        if ($diseasegroupid) {
            $shopProductTypes = ShopProductTypeDao::getListByDiseaseGroupid($diseasegroupid);
        }

        $cond = "";
        $bind = [];

        if ($diseasegroupid) {
            $cond .= " and b.diseasegroupid=:diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        if ($shopproducttypeid > 0) {
            $cond .= " and a.shopproducttypeid=:shopproducttypeid ";
            $bind[":shopproducttypeid"] = $shopproducttypeid;
        }

        if ($status < 2) {
            $cond .= " and a.status=:status ";
            $bind[":status"] = $status;
        }
        if($medicine_type == "yes"){
            $cond .= " and a.objid > 0 and a.objtype = 'MedicineProduct' ";
        }

        if($medicine_type == "no"){
            $cond .= " and a.objid = 0 and a.objtype = '' ";
        }

        $sql = "select a.*
            from shopproducts a
            inner join shopproducttypes b on b.id = a.shopproducttypeid
            where 1=1 " . $cond . " order by shopproducttypeid , pos, title_pinyin ";
        $shopProducts = Dao::loadEntityList("ShopProduct", $sql, $bind);

        XContext::setValue('shopproducttypeid', $shopproducttypeid);
        XContext::setValue('status', $status);

        XContext::setValue('shopProductTypes', $shopProductTypes);
        XContext::setValue('shopProducts', $shopProducts);
        XContext::setValue('diseasegroupid', $diseasegroupid);
        XContext::setValue('medicine_type', $medicine_type);

        XContext::setValue('startdate', $startdate);
        XContext::setValue('enddate', $enddate);
        XContext::setValue('thedate', $thedate);

        return self::SUCCESS;
    }

    // 导出
    public function doListForSumPriceOutput () {
        $diseasegroupid = XRequest::getValue('diseasegroupid', 2);
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $status = XRequest::getValue('status', 2);
        $medicine_type = XRequest::getValue('medicine_type', 'all');

        //时间维度
        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 6 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $cond = "";
        $bind = [];

        if ($diseasegroupid) {
            $cond .= " and b.diseasegroupid=:diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        if ($shopproducttypeid > 0) {
            $cond .= " and a.shopproducttypeid=:shopproducttypeid ";
            $bind[":shopproducttypeid"] = $shopproducttypeid;
        }

        if ($status < 2) {
            $cond .= " and a.status=:status ";
            $bind[":status"] = $status;
        }

        if($medicine_type == "yes"){
            $cond .= " and a.objid > 0 and a.objtype = 'MedicineProduct' ";
        }

        if($medicine_type == "no"){
            $cond .= " and a.objid = 0 and a.objtype = '' ";
        }

        $sql = "select a.*
            from shopproducts a
            inner join shopproducttypes b on b.id = a.shopproducttypeid
            where 1=1 " . $cond . " order by shopproducttypeid , pos, title_pinyin ";
        $shopProducts = Dao::loadEntityList("ShopProduct", $sql, $bind);
        $data = array();
        foreach ($shopProducts as $a) {
            $temp = array();
            $temp[] = $a->id;
            $temp[] = $a->shopproducttype->name;
            $temp[] = $a->title;

            //规格
            $obj = $a->obj;
            if($obj instanceof MedicineProduct){
                $temp[] = $obj->size_pack;
            }else{
                $temp[] = "";
            }
            //生产企业
            $temp[] = $a->getCompanyName();
            $stockItem = StockItemDao::getLastByShopProduct($a);
            if($stockItem instanceof StockItem){
                //最后进价
                $temp[] = $stockItem->getPrice_yuan();
                //最后渠道
                $temp[] = $stockItem->sourse;
            }else{
                $temp[] = "";
                $temp[] = "";
            }
            $temp[] = $a->getPrice_yuan();
            $temp[] = $a->getMarket_price_yuan();
            $temp[] = $a->left_cnt;
            $temp[] = $a->getStockSumPrice_yuan();
            $temp[] = $a->getStockCnt($thedate);
            $temp[] = $a->getStockSumPrice_yuan($thedate);

            $saled_profile = $a->getSaledProfile($startdate, $enddate);
            $temp[] = sprintf("%.0f", $saled_profile["cnt"]);
            $temp[] = sprintf("%.2f", $saled_profile["saled_amount"]);
            $temp[] = sprintf("%.2f", $saled_profile["cost_amount"]);
            $data[] = $temp;
        }
        $headarr = array(
            "id",
            "类别",
            "标题",
            "规格",
            "生产企业",
            "最后进价",
            "最后渠道",
            "单价",
            "市场价",
            "当前库存量",
            "当前库存金额",
            "{$thedate}库存量",
            "{$thedate}库存金额",
            "销售数量",
            "销售金额",
            "成本金额",
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

}
