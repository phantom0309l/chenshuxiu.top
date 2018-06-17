<?php

class FaqLogMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $faqid = XRequest::getValue("faqid", 0);
        $auditorid = XRequest::getValue("auditorid", 0);

        $cond = "";
        $bind = [];

        //faqid筛选
        if($faqid > 0){
            $cond .= " and faqid = :faqid ";
            $bind[":faqid"] = $faqid;
        }

        //auditorid筛选
        if($auditorid > 0){
            $cond .= " and auditorid = :auditorid ";
            $bind[":auditorid"] = $auditorid;
        }

        //获得实体
        $sql = "select *
                from faqlogs
                where 1 = 1 {$cond} order by id";
        $faqLogs = Dao::loadEntityList4Page("FaqLog", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("faqLogs", $faqLogs);

        //获得分页
        $countSql = "select count(*)
                    from faqlogs
                    where 1 = 1 {$cond} order by id";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/faqlogmgr/list?1=1";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("faqid", $faqid);
        return self::SUCCESS;
    }

}
