<?php

class AuditorPushMsgTplMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $auditorpushmsgtplid = XRequest::getValue("auditorpushmsgtplid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($auditorpushmsgtplid > 0){
            $cond .= " and auditorpushmsgtplid = :auditorpushmsgtplid ";
            $bind[":auditorpushmsgtplid"] = $auditorpushmsgtplid;
        }

        //获得实体
        $sql = "select *
                    from auditorpushmsgtpls
                    where 1 = 1 {$cond} order by id desc";
        $auditorPushMsgTpls = Dao::loadEntityList4Page("AuditorPushMsgTpl", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("auditorPushMsgTpls", $auditorPushMsgTpls);

        //获得分页
        $countSql = "select count(*)
                    from auditorpushmsgtpls
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/auditorpushmsgtplmgr/list?auditorpushmsgtplid={$auditorpushmsgtplid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("auditorpushmsgtplid", $auditorpushmsgtplid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne () {
        $auditorpushmsgtplid = XRequest::getValue("auditorpushmsgtplid", 0);

        $auditorPushMsgTpl = AuditorPushMsgTpl::getById($auditorpushmsgtplid);

        XContext::setValue("auditorPushMsgTpl", $auditorPushMsgTpl);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {

        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", "");
        $content = XRequest::getValue("content", "");

        $row = array();
        $row["title"] = $title;
        $row["ename"] = $ename;
        $row["content"] = $content;

        AuditorPushMsgTpl::createByBiz($row);

        XContext::setJumpPath("/auditorpushmsgtplmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $auditorpushmsgtplid = XRequest::getValue("auditorpushmsgtplid", 0);

        $auditorPushMsgTpl = AuditorPushMsgTpl::getById($auditorpushmsgtplid);
        DBC::requireTrue($auditorPushMsgTpl instanceof AuditorPushMsgTpl, "auditorPushMsgTpl不存在:{$auditorpushmsgtplid}");
        XContext::setValue("auditorPushMsgTpl", $auditorPushMsgTpl);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $auditorpushmsgtplid = XRequest::getValue("auditorpushmsgtplid", 0);
        $title = XRequest::getValue("title", '');
        $ename = XRequest::getValue("ename", '');
        $content = XRequest::getValue("content", "");

        $auditorPushMsgTpl = AuditorPushMsgTpl::getById($auditorpushmsgtplid);
        DBC::requireTrue($auditorPushMsgTpl instanceof AuditorPushMsgTpl, "auditorPushMsgTpl不存在:{$auditorpushmsgtplid}");

        $auditorPushMsgTpl->title = $title;
        $auditorPushMsgTpl->ename = $ename;
        $auditorPushMsgTpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/auditorpushmsgtplmgr/modify?auditorpushmsgtplid=" . $auditorpushmsgtplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
