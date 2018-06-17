<?php

class DoctorApplyMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorapplyid = XRequest::getValue("doctorapplyid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($doctorapplyid > 0){
            $cond .= " and id = :id ";
            $bind[":id"] = $doctorapplyid;
        }

        //获得实体
        $sql = "select *
                    from doctorapplys
                    where 1 = 1 {$cond} order by id desc";
        $doctorApplys = Dao::loadEntityList4Page("DoctorApply", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctorApplys", $doctorApplys);

        //获得分页
        $countSql = "select count(*)
                    from doctorapplys
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorapplymgr/list?doctorapplyid={$doctorapplyid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("doctorapplyid", $doctorapplyid);
        return self::SUCCESS;
    }
}
