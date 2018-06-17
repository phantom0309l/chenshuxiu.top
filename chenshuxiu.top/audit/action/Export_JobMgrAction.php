<?php

class Export_JobMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $export_jobid = XRequest::getValue("export_jobid", 0);
        $type = XRequest::getValue("type", "all");
        $fuwu = XRequest::getValue("fuwu", "");

        $myauditor = $this->myauditor;

        $cond = "";
        $bind = [];

        //id筛选
        if($export_jobid > 0){
            $cond .= " and export_jobid = :export_jobid ";
            $bind[":export_jobid"] = $export_jobid;
        }

        //类型筛选
        if($type != "all"){
            $cond .= " and type = :type ";
            $bind[":type"] = $type;
        }

        //服务类型
        if( empty($fuwu) ){
            $cond .= " and type not in ('shoporder_service', 'shoporder_service2') ";
        }

        //auditorid
        if($myauditor instanceof Auditor){
            $cond .= " and auditorid = :auditorid ";
            $bind[":auditorid"] = $myauditor->id;
        }

        //获得实体
        $sql = "select *
                    from export_jobs
                    where 1 = 1 {$cond} order by id desc";
        $export_Jobs = Dao::loadEntityList4Page("Export_Job", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("export_Jobs", $export_Jobs);

        //获得分页
        $countSql = "select count(*)
                    from export_jobs
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/export_jobmgr/list?export_jobid={$export_jobid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("export_jobid", $export_jobid);
        XContext::setValue("type", $type);

        $_myuserid_ = XRequest::getValue('_myuserid_', '');
        XContext::setValue("_myuserid_", $_myuserid_);
        return self::SUCCESS;
    }
}
