<?php

class MgtGroupMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $mgtgroupid = XRequest::getValue("mgtgroupid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($mgtgroupid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $mgtgroupid;
        }

        //获得实体
        $sql = "select *
                    from mgtgroups
                    where 1 = 1 {$cond} order by id desc";
        $mgtGroups = Dao::loadEntityList4Page("MgtGroup", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("mgtGroups", $mgtGroups);

        //获得分页
        $countSql = "select count(*)
                    from mgtgroups
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/mgtgroupmgr/list?mgtgroupid={$mgtgroupid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("mgtgroupid", $mgtgroupid);
        return self::SUCCESS;
    }
}
        