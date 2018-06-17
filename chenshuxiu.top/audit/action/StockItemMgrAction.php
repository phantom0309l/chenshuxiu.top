<?php

class StockItemMgrAction extends AuditBaseAction
{
    // 库存列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $shopproductid = XRequest::getValue("shopproductid", 0);
        $in_time_start = XRequest::getValue("in_time_start", '');
        $in_time_end = XRequest::getValue("in_time_end", '');
        $expire_date = XRequest::getValue("expire_date", '');
        $sourse = XRequest::getValue("sourse", '');
        $the_date = XRequest::getValue("the_date", '');
        $pay_type = XRequest::getValue("pay_type", 0);
        $has_invoice = XRequest::getValue("has_invoice", -1);

        $cond = '';
        $bind = [];

        //商品筛选
        if ($shopproductid > 0) {
            $cond .= " and shopproductid = :shopproductid ";
            $bind[":shopproductid"] = $shopproductid;
        }

        //入库时间筛选
        if ($in_time_start > 0) {
            $cond .= " and in_time >= :in_time_start ";
            $bind[":in_time_start"] = $in_time_start;
        }

        if ($in_time_end > 0) {
            $cond .= " and in_time < :in_time_end ";
            $in_time_end_fix = date("Y-m-d", strtotime($in_time_end) + 86400);
            $bind[":in_time_end"] = $in_time_end_fix;
        }

        //过期时间筛选
        if ($expire_date > 0) {
            $cond .= " and expire_date = :expire_date ";
            $bind[":expire_date"] = $expire_date;
        }

        //渠道，来源筛选
        if ($sourse > 0) {
            $cond .= " and sourse = :sourse ";
            $bind[":sourse"] = $sourse;
        }

        //账期
        if ('' != $the_date) {
            $cond .= " and the_date = :the_date ";
            $bind[":the_date"] = $the_date;
        }

        //付款方式
        if ($pay_type > 0) {
            $cond .= " and pay_type = :pay_type ";
            $bind[":pay_type"] = $pay_type;
        }

        //有无发票
        if ($has_invoice >= 0) {
            $cond .= " and has_invoice = :has_invoice ";
            $bind[":has_invoice"] = $has_invoice;
        }

        //获得实体
        $sql = "select *
                    from stockitems
                    where 1 = 1 {$cond} order by id desc";
        $stockItems = Dao::loadEntityList4Page("StockItem", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("stockItems", $stockItems);

        //获得分页
        $countSql = "select count(*)
                    from stockitems
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/stockitemmgr/list?shopproductid={$shopproductid}&in_time_start={$in_time_start}&in_time_end={$in_time_end}&expire_date={$expire_date}&sourse={$sourse}&the_date={$the_date}&pay_type={$pay_type}&has_invoice={$has_invoice}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("shopproductid", $shopproductid);
        XContext::setValue("in_time_start", $in_time_start);
        XContext::setValue("in_time_end", $in_time_end);
        XContext::setValue("expire_date", $expire_date);
        XContext::setValue("sourse", $sourse);
        XContext::setValue("the_date", $the_date);
        XContext::setValue("pay_type", $pay_type);
        XContext::setValue("has_invoice", $has_invoice);

        $shopProducts = Dao::getEntityListByCond('ShopProduct');
        XContext::setValue("shopProducts", $shopProducts);

        return self::SUCCESS;
    }

    //导出库存明细
    public function doListOutput() {
        $shopproductid = XRequest::getValue("shopproductid", 0);
        $in_time_start = XRequest::getValue("in_time_start", '');
        $in_time_end = XRequest::getValue("in_time_end", '');
        $expire_date = XRequest::getValue("expire_date", '');
        $sourse = XRequest::getValue("sourse", '');

        $cond = '';
        $bind = [];

        //商品筛选
        if ($shopproductid > 0) {
            $cond .= " and shopproductid = :shopproductid ";
            $bind[":shopproductid"] = $shopproductid;
        }

        //入库时间筛选
        if ($in_time_start > 0) {
            $cond .= " and in_time >= :in_time_start ";
            $bind[":in_time_start"] = $in_time_start;
        }

        if ($in_time_end > 0) {
            $cond .= " and in_time < :in_time_end ";
            $in_time_end_fix = date("Y-m-d", strtotime($in_time_end) + 86400);
            $bind[":in_time_end"] = $in_time_end_fix;
        }

        //过期时间筛选
        if ($expire_date > 0) {
            $cond .= " and expire_date = :expire_date ";
            $bind[":expire_date"] = $expire_date;
        }

        //渠道，来源筛选
        if ($sourse > 0) {
            $cond .= " and sourse = :sourse ";
            $bind[":sourse"] = $sourse;
        }

        //获得实体
        $sql = "select id
                    from stockitems
                    where 1 = 1 {$cond} order by id desc";
        $ids = Dao::queryValues($sql, $bind);
        $data = array();
        foreach ($ids as $id) {
            $stockItem = StockItem::getById($id);
            if ($stockItem instanceof StockItem) {
                $temp = array();
                $temp[] = $stockItem->id;
                $temp[] = $stockItem->shopproduct->title;
                $temp[] = $stockItem->getPrice_yuan();
                $temp[] = $stockItem->cnt;
                $temp[] = $stockItem->left_cnt;
                $temp[] = substr($stockItem->in_time, 0, 10);
                $temp[] = $stockItem->expire_date;
                $temp[] = $stockItem->batch_number;
                $temp[] = $stockItem->sourse;
                $temp[] = $stockItem->auditor->name;
                $temp[] = $stockItem->remark;
                $data[] = $temp;
            }
        }
        $headarr = array(
            "id",
            "商品",
            "价格",
            "入库量",
            "当前剩余",
            "入库时间",
            "过期时间",
            "生产批号",
            "渠道",
            "入库人",
            "备注"
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    public function doAdd() {
        $shopproductid = XRequest::getValue("shopproductid", 0);
        $shopProduct = ShopProduct::getById($shopproductid);
        XContext::setValue("shopProduct", $shopProduct);
        return self::SUCCESS;
    }

    public function doAddPost() {
        $myauditor = $this->myauditor;

        $shopproductid = XRequest::getValue("shopproductid", 0);
        DBC::requireTrue($shopproductid > 0, "请选择商品");
        $price = XRequest::getValue("price", 0);
        DBC::requireTrue($price > 0, "请输入正确的价格");
        $cnt = XRequest::getValue("cnt", 0);
        DBC::requireTrue($cnt > 0, "请输入正确的数量");
        $batch_number = XRequest::getValue("batch_number", "");
        $in_time = XRequest::getValue("in_time", date("Y-m-d H:i:s", time()));
        $expire_date = XRequest::getValue("expire_date", "");
        $sourse = XRequest::getValue("sourse", "");
        $order_person = XRequest::getValue("order_person", "");
        $pay_person = XRequest::getValue("pay_person", "");
        $the_date = XRequest::getValue("the_date", "0000-00-00");
        $pay_type = XRequest::getValue("pay_type", 0);
        $has_invoice = XRequest::getValue("has_invoice", 0);
        $remark = XRequest::getValue("remark", "");

        $row = array();
        $row["shopproductid"] = $shopproductid;
        $row["price"] = $price * 100;
        $row["cnt"] = $cnt;
        $row["left_cnt"] = $cnt;
        $row["batch_number"] = $batch_number;
        $row["in_time"] = $in_time;
        $row["expire_date"] = $expire_date;
        $row["sourse"] = $sourse;
        $row["order_person"] = $order_person;
        $row["pay_person"] = $pay_person;
        $row["the_date"] = $the_date;
        $row["pay_type"] = $pay_type;
        $row["has_invoice"] = $has_invoice;
        $row["auditorid"] = $myauditor->id;
        $row["remark"] = $remark;

        StockItem::createByBiz($row);

        $shopProduct = ShopProduct::getById($shopproductid);
        $shopProduct->left_cnt += $cnt;

        ShopProductNoticeService::pushNoticesByShopProduct($shopProduct);

        XContext::setJumpPath("/stockitemmgr/list");

        return self::SUCCESS;
    }

    public function doModify() {
        $stockitemid = XRequest::getValue("stockitemid", 0);

        $stockItem = StockItem::getById($stockitemid);
        DBC::requireTrue($stockItem instanceof StockItem, "stockitem不存在:{$stockitemid}");
        XContext::setValue("stockItem", $stockItem);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $myauditor = $this->myauditor;

        $stockitemid = XRequest::getValue("stockitemid", 0);
        $batch_number = XRequest::getValue("batch_number", "");
        $in_time = XRequest::getValue("in_time", "");
        $expire_date = XRequest::getValue("expire_date", "");
        $sourse = XRequest::getValue("sourse", "");
        $order_person = XRequest::getValue("order_person", "");
        $pay_person = XRequest::getValue("pay_person", "");
        $the_date = XRequest::getValue("the_date", "0000-00-00");
        $pay_type = XRequest::getValue("pay_type", 0);
        $has_invoice = XRequest::getValue("has_invoice", 0);
        $remark = XRequest::getValue("remark", "");

        $stockItem = StockItem::getById($stockitemid);
        DBC::requireTrue($stockItem instanceof StockItem, "stockitem不存在:{$stockitemid}");

        $stockItem->batch_number = $batch_number;
        $stockItem->expire_date = $expire_date;
        $stockItem->in_time = $in_time;
        $stockItem->sourse = $sourse;
        $stockItem->order_person = $order_person;
        $stockItem->pay_person = $pay_person;
        $stockItem->the_date = $the_date;
        $stockItem->pay_type = $pay_type;
        $stockItem->has_invoice = $has_invoice;
        $stockItem->set4lock("auditorid", $myauditor->id);
        $stockItem->remark = $remark;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/stockitemmgr/modify?stockitemid=" . $stockitemid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
