<?php

class AuditorOpLogMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    
    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $patientid = XRequest::getValue('patientid', '');

        $date_range = XRequest::getValue('date_range', '');
        $auditorid = XRequest::getValue('auditorid', 0);
        $code = XRequest::getValue('code', '');

        $cond = "";
        $bind = [];

        if ($patientid) {
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        // 日期
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];

            $cond .= " and createtime >= '$from_date' and createtime < '$to_date' ";
        }

        if ($auditorid) {
            $cond .= " and auditorid = :auditorid ";
            $bind[':auditorid'] = $auditorid;
        }

        if ($code) {
            $cond .= " and code = :code ";
            $bind[':code'] = $code;
        }

        $cond .= " order by createtime desc ";

        //获得实体
        $auditorOpLogs = Dao::getEntityListByCond4Page("AuditorOpLog", $pagesize, $pagenum, $cond, $bind);
        XContext::setValue("auditorOpLogs", $auditorOpLogs);

        //获得分页
        $countSql = "select count(*)
                    from auditoroplogs
                    where 1 = 1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/auditoroplogmgr/list?patientid={$patientid}&auditorid={$auditorid}&code={$code}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("patientid", $patientid);
        XContext::setValue("auditorid", $auditorid);
        XContext::setValue("code", $code);
        XContext::setValue("date_range", $date_range);

        return self::SUCCESS;
    }
    
    // 详情页
    public function doOne () {
        $auditoroplogid = XRequest::getValue("auditoroplogid", 0);

        $auditorOpLog = AuditorOpLog::getById($auditoroplogid);

        XContext::setValue("auditorOpLog", $auditorOpLog);
        return self::SUCCESS;
    }
    
    public function doAdd () {
        return self::SUCCESS;
    }
    
    public function doAddPost () {

        $auditorid = XRequest::getValue("auditorid", 0);
        $code = XRequest::getValue("code", '');
        $content = XRequest::getValue("content", "");
        

        $row = array();
        $row["auditorid"] = $auditorid;
        $row["code"] = $code;
        $row["content"] = $content;
        

        AuditorOpLog::createByBiz($row);

        XContext::setJumpPath("/auditoroplogmgr/list");
        return self::SUCCESS;
    }
    
    public function doModify () {
        $auditoroplogid = XRequest::getValue("auditoroplogid", 0);

        $auditorOpLog = AuditorOpLog::getById($auditoroplogid);
        DBC::requireTrue($auditorOpLog instanceof AuditorOpLog, "auditorOpLog不存在:{$auditoroplogid}");
        XContext::setValue("auditorOpLog", $auditorOpLog);

        return self::SUCCESS;
    }
    
    // 修改提交
    public function doModifyPost () {
        $auditoroplogid = XRequest::getValue("auditoroplogid", 0);
        $auditorid = XRequest::getValue("auditorid", 0);
        $code = XRequest::getValue("code", '');
        $content = XRequest::getValue("content", "");
        
        $auditorOpLog = AuditorOpLog::getById($auditoroplogid);
        DBC::requireTrue($auditorOpLog instanceof AuditorOpLog, "auditorOpLog不存在:{$auditoroplogid}");

        $auditorOpLog->auditorid = $auditorid;
        $auditorOpLog->code = $code;
        $auditorOpLog->content = $content;
        
        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/auditoroplogmgr/modify?auditoroplogid=" . $auditoroplogid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
        