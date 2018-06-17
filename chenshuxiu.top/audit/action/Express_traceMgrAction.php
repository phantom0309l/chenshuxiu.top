<?php

class Express_traceMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $express_traceid = XRequest::getValue("express_traceid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($express_traceid > 0){
            $cond .= " and id = :express_traceid ";
            $bind[":express_traceid"] = $express_traceid;
        }

        //获得实体
        $sql = "select *
                    from express_traces
                    where 1 = 1 {$cond} order by id desc";
        $express_traces = Dao::loadEntityList4Page("Express_trace", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("express_traces", $express_traces);

        //获得分页
        $countSql = "select count(*)
                    from express_traces
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/express_tracemgr/list?express_traceid={$express_traceid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("express_traceid", $express_traceid);
        return self::SUCCESS;
    }
}
