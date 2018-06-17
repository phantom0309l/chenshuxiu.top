<?php

class ShopOrderItem_lackMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $shoporderitem_lackid = XRequest::getValue("shoporderitem_lackid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($shoporderitem_lackid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $shoporderitem_lackid;
        }

        //获得实体
        $sql = "select *
                    from shoporderitem_lacks
                    where 1 = 1 {$cond} order by id desc";
        $shopOrderItem_lacks = Dao::loadEntityList4Page("ShopOrderItem_lack", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopOrderItem_lacks", $shopOrderItem_lacks);

        //获得分页
        $countSql = "select count(*)
                    from shoporderitem_lacks
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shoporderitem_lackmgr/list?shoporderitem_lackid={$shoporderitem_lackid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("shoporderitem_lackid", $shoporderitem_lackid);
        return self::SUCCESS;
    }
}
        