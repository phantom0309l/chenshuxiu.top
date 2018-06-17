<?php

class ShopProductNoticeMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $shopProductNoticeId = XRequest::getValue("shopProductNoticeId", 0);
        $shopProductId = XRequest::getValue("shopProductId", 0);
        $status = XRequest::getValue("status", -1);

        $cond = "";
        $bind = [];

        //id筛选
        if ($shopProductNoticeId > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $shopProductNoticeId;
        }

        //shopProductId筛选
        if ($shopProductId > 0) {
            $cond .= " and shopproductid = :shopproductid ";
            $bind[":shopproductid"] = $shopProductId;
        }

        if ($status > -1) {
            $cond .= " and status = :status ";
            $bind[":status"] = $status;
        }

        //获得实体
        $sql = "select *
                    from shopproductnotices
                    where 1 = 1 {$cond} order by id desc";
        $shopProductNotices = Dao::loadEntityList4Page("ShopProductNotice", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopProductNotices", $shopProductNotices);

        //获得分页
        $countSql = "select count(*)
                    from shopproductnotices
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shopproductnoticemgr/list?shopProductNoticeId={$shopProductNoticeId}&shopProductId={$shopProductId}&status={$status}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        $shopProducts = Dao::getEntityListByCond('ShopProduct', " and objtype = 'MedicineProduct' ");
        XContext::setValue("shopProducts", $shopProducts);

        XContext::setValue("shopProductNoticeId", $shopProductNoticeId);
        XContext::setValue("shopProductId", $shopProductId);
        XContext::setValue("status", $status);
        return self::SUCCESS;
    }

    public function doPushJson() {
        $shopProductNoticeId = XRequest::getValue("shopProductNoticeId", 0);
        DBC::requireTrue($shopProductNoticeId, "[shopProductNoticeId]不能为空！");
        $shopProductNotice = ShopProductNotice::getById($shopProductNoticeId);

        ShopProductNoticeService::pushNotice($shopProductNotice);

        $this->result['errmsg'] = $shopProductNotice->getStatusStr();
        return self::TEXTJSON;
    }
}
        