<?php

class MgtGroupTplMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $mgtgrouptplid = XRequest::getValue("mgtgrouptplid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($mgtgrouptplid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $mgtgrouptplid;
        }

        //获得实体
        $sql = "select *
                    from mgtgrouptpls
                    where 1 = 1 {$cond} order by id desc";
        $mgtGroupTpls = Dao::loadEntityList4Page("MgtGroupTpl", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("mgtGroupTpls", $mgtGroupTpls);

        //获得分页
        $countSql = "select count(*)
                    from mgtgrouptpls
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/mgtgrouptplmgr/list?mgtgrouptplid={$mgtgrouptplid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("mgtgrouptplid", $mgtgrouptplid);
        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {

        $ename = XRequest::getValue("ename", "");
        $title = XRequest::getValue("title", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");


        $row = array();
        $row["ename"] = $ename;
        $row["title"] = $title;
        $row["brief"] = $brief;
        $row["content"] = $content;


        MgtGroupTpl::createByBiz($row);

        XContext::setJumpPath("/mgtgrouptplmgr/list");
        return self::SUCCESS;
    }

    public function doModify() {
        $mgtgrouptplid = XRequest::getValue("mgtgrouptplid", 0);

        $mgtGroupTpl = MgtGroupTpl::getById($mgtgrouptplid);
        DBC::requireTrue($mgtGroupTpl instanceof MgtGroupTpl, "mgtGroupTpl不存在:{$mgtgrouptplid}");
        XContext::setValue("mgtGroupTpl", $mgtGroupTpl);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $mgtgrouptplid = XRequest::getValue("mgtgrouptplid", 0);
        $ename = XRequest::getValue("ename", "");
        $title = XRequest::getValue("title", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");

        $mgtGroupTpl = MgtGroupTpl::getById($mgtgrouptplid);
        DBC::requireTrue($mgtGroupTpl instanceof MgtGroupTpl, "mgtGroupTpl不存在:{$mgtgrouptplid}");

        $mgtGroupTpl->ename = $ename;
        $mgtGroupTpl->title = $title;
        $mgtGroupTpl->brief = $brief;
        $mgtGroupTpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/mgtgrouptplmgr/modify?mgtgrouptplid=" . $mgtgrouptplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
        