<?php

class MgtPlanMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $mgtplanid = XRequest::getValue("mgtplanid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($mgtplanid > 0){
            $cond .= " and mgtplanid = :mgtplanid ";
            $bind[":mgtplanid"] = $mgtplanid;
        }

        //获得实体
        $sql = "select *
                    from mgtplans
                    where 1 = 1 {$cond} order by id desc";
        $mgtPlans = Dao::loadEntityList4Page("MgtPlan", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("mgtPlans", $mgtPlans);

        //获得分页
        $countSql = "select count(*)
                    from mgtplans
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/mgtplanmgr/list?mgtplanid={$mgtplanid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("mgtplanid", $mgtplanid);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {

        $ename = XRequest::getValue("ename", "" );
        $title = XRequest::getValue("title", "");
        $brief = XRequest::getValue("brief", "");

        $row = array();
        $row["ename"] = $ename;
        $row["title"] = $title;
        $row["brief"] = $brief;

        MgtPlan::createByBiz($row);

        XContext::setJumpPath("/mgtplanmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $mgtplanid = XRequest::getValue("mgtplanid", 0);

        $mgtPlan = MgtPlan::getById($mgtplanid);
        DBC::requireTrue($mgtPlan instanceof MgtPlan, "mgtPlan不存在:{$mgtplanid}");
        XContext::setValue("mgtPlan", $mgtPlan);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $mgtplanid = XRequest::getValue("mgtplanid", 0);
        $ename = XRequest::getValue("ename", "");
        $title = XRequest::getValue("title", "");
        $brief = XRequest::getValue("brief", "");

        $mgtPlan = MgtPlan::getById($mgtplanid);
        DBC::requireTrue($mgtPlan instanceof MgtPlan, "mgtPlan不存在:{$mgtplanid}");

        $mgtPlan->ename = $ename;
        $mgtPlan->title = $title;
        $mgtPlan->brief = $brief;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/mgtplanmgr/modify?mgtplanid=" . $mgtplanid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
