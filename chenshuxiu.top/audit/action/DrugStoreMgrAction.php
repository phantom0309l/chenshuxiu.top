<?php

class DrugStoreMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $cond = "";
        $bind = [];

        //获得实体
        $sql = "select *
                    from drugstores
                    where 1 = 1 {$cond} order by id desc";
        $drugStores = Dao::loadEntityList4Page("DrugStore", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("drugStores", $drugStores);

        //获得分页
        $countSql = "select count(*)
                    from drugstores
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/drugstoremgr/list";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 详情页
    public function doOne () {
        $drugstoreid = XRequest::getValue("drugstoreid", 0);

        $drugStore = DrugStore::getById($drugstoreid);

        XContext::setValue("drugStore", $drugStore);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {

        $title = XRequest::getValue("title", '');
        $xprovinceid = XRequest::getValue("xprovinceid", 0);
        $xcityid = XRequest::getValue("xcityid", 0);
        $xquid = XRequest::getValue("xquid", 0);
        $content = XRequest::getValue("content", '');
        $mobile = XRequest::getValue("mobile", '');

        $row = array();
        $row["title"] = $title;
        $row["xprovinceid"] = $xprovinceid;
        $row["xcityid"] = $xcityid;
        $row["xquid"] = $xquid;
        $row["content"] = $content;
        $row["mobile"] = $mobile;

        DrugStore::createByBiz($row);

        XContext::setJumpPath("/drugstoremgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $drugstoreid = XRequest::getValue("drugstoreid", 0);

        $drugStore = DrugStore::getById($drugstoreid);
        DBC::requireTrue($drugStore instanceof DrugStore, "drugStore不存在:{$drugstoreid}");
        XContext::setValue("drugStore", $drugStore);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $drugstoreid = XRequest::getValue("drugstoreid", 0);
        $title = XRequest::getValue("title", '');
        $xprovinceid = XRequest::getValue("xprovinceid", 0);
        $xcityid = XRequest::getValue("xcityid", 0);
        $xquid = XRequest::getValue("xquid", 0);
        $content = XRequest::getValue("content", '');
        $mobile = XRequest::getValue("mobile", '');

        $drugStore = DrugStore::getById($drugstoreid);
        DBC::requireTrue($drugStore instanceof DrugStore, "drugStore不存在:{$drugstoreid}");

        $drugStore->title = $title;
        $drugStore->xprovinceid = $xprovinceid;
        $drugStore->xcityid = $xcityid;
        $drugStore->xquid = $xquid;
        $drugStore->content = $content;
        $drugStore->mobile = $mobile;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/drugstoremgr/modify?drugstoreid=" . $drugstoreid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
        