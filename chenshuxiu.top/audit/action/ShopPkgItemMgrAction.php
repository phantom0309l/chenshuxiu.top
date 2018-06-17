<?php

class ShopPkgItemMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $shoppkgitemid = XRequest::getValue("shoppkgitemid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($shoppkgitemid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $shoppkgitemid;
        }

        //获得实体
        $sql = "select *
                    from shoppkgitems
                    where 1 = 1 {$cond} order by id desc";
        $shopPkgItems = Dao::loadEntityList4Page("ShopPkgItem", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopPkgItems", $shopPkgItems);

        //获得分页
        $countSql = "select count(*)
                    from shoppkgitems
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shoppkgitemmgr/list?shoppkgitemid={$shoppkgitemid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("shoppkgitemid", $shoppkgitemid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne() {
        $shoppkgitemid = XRequest::getValue("shoppkgitemid", 0);

        $shopPkgItem = ShopPkgItem::getById($shoppkgitemid);

        XContext::setValue("shopPkgItem", $shopPkgItem);
        return self::SUCCESS;
    }

    public function doModifyCntJson() {
        $shopPkgItemId = XRequest::getValue("shoppkgitemid", 0);
        $cnt = XRequest::getValue("cnt", 0);

        $shopPkgItem = ShopPkgItem::getById($shopPkgItemId);

        DBC::requireTrue(intval($cnt), "数量不可以修改为0！");
        DBC::requireTrue($shopPkgItem instanceof ShopPkgItem, "没有找到配送单明细！");

        DBC::requireTrue($shopPkgItem->shoppkg->canChange(), '此配送单，不可修改！');

        // 要增加数量
        $changCnt = $cnt - $shopPkgItem->cnt;
        if ($changCnt > 0) {
            $shopOrderItem = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopPkgItem->shoppkg->shoporder, $shopPkgItem->shopproduct);
            // 要增加的数量一定要小于、等于可配的数量
            DBC::requireTrue($shopOrderItem->getCanPkgCnt() >= $changCnt, "运营修改配送单增加商品[{$changCnt}]数量超出了可配的数量[{$shopOrderItem->getCanPkgCnt()}]！");
        }

        $shopPkgItem->cnt = $cnt;

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }

    public function doDeleteJson() {
        $shopPkgItemId = XRequest::getValue('shoppkgitemid', 0);
        $shopPkgItem = ShopPkgItem::getById($shopPkgItemId);
        DBC::requireTrue($shopPkgItem instanceof ShopPkgItem, "未找到shoppkgitem，【shoppkgitemid={$shopPkgItemId}】");

        DBC::requireTrue($shopPkgItem->shoppkg->canChange(), '此配送单，不可删除！');

        $shopPkg = $shopPkgItem->shoppkg;
        DBC::requireTrue($shopPkg instanceof ShopPkg, "未找到shoppkg，【shoppkgitemid={$shopPkgItemId}】");
        $shopPkgItems = $shopPkg->getShopPkgItems();

        if (1 == count($shopPkgItems)) {
            ShopPkgService::deleteShopPkg($shopPkg);
        } else {
            $shopPkgItem->remove();
        }

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }
}
