<?php

class OptLogMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $auditorid = XRequest::getValue("auditorid", 0);
        $optask_op_date_range = XRequest::getValue("optask_op_date_range", "");
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $optask_plan_date_range = XRequest::getValue("optask_plan_date_range", "");

        $cond = "";
        $bind = [];

        if ($auditorid) {
            $cond .= " and a.auditorid = :auditorid ";
            $bind[':auditorid'] = $auditorid;
        }

        if ($optasktplid) {
            $cond .= " and b.optasktplid = :optasktplid ";
            $bind[':optasktplid'] = $optasktplid;
        }

        if (!empty($optask_op_date_range)) {
            $arr = explode('至', $optask_op_date_range);
            $from_date = trim($arr[0]);
            $to_date = trim($arr[1]);
            $to_date = $to_date . ' 23:59:59';

            $cond .= " AND a.createtime BETWEEN '{$from_date}' AND '{$to_date}' ";
        }

        if (!empty($optask_plan_date_range)) {
            $arr = explode('至', $optask_plan_date_range);
            $from_date = trim($arr[0]);
            $to_date = trim($arr[1]);
            $to_date = $to_date . ' 23:59:59';

            $cond .= " AND b.plantime BETWEEN '{$from_date}' AND '{$to_date}' ";
        }

        $cond .= " order by a.id, b.id ";

        $sql = "select a.*
                from optlogs a
                inner join optasks b on b.id=a.optaskid
                inner join optasktpls c on c.id=b.optasktplid
                inner join patients p on p.id=b.patientid
                where 1 = 1 {$cond} ";
        $optlogs = Dao::loadEntityList4Page('OptLog', $sql, $pagesize, $pagenum, $bind);

        $countSql = "select count(a.id)
                from optlogs a
                inner join optasks b on b.id=a.optaskid
                inner join optasktpls c on c.id=b.optasktplid
                inner join patients p on p.id=b.patientid
                where 1 = 1 {$cond} ";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/optlogmgr/list?auditorid={$auditorid}&optask_op_date_range={$optask_op_date_range}&optasktplid={$optasktplid}&optask_plan_date_range={$optask_plan_date_range}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('optlogs', $optlogs);
        XContext::setValue('auditorid', $auditorid);
        XContext::setValue('optask_op_date_range', $optask_op_date_range);
        XContext::setValue('optasktplid', $optasktplid);
        XContext::setValue('optask_plan_date_range', $optask_plan_date_range);
        XContext::setValue('pagelink', $pagelink);

        return self::SUCCESS;
    }

    public function doAddJson () {
        $optaskid = XRequest::getValue("optaskid", 0);
        $plantime = XRequest::getValue("plantime", "");
        $followtype = XRequest::getValue("followtype", 1);
        $content = XRequest::getValue("content", "");

        $myauditor = $this->myauditor;

        $temp = array();
        $temp['optaskid'] = $optaskid;
        $temp['auditorid'] = $myauditor->id;
        $temp['domode'] = $followtype;
        $temp['content'] = $content;
        $temp['plantime'] = $plantime;
        $jsoncontent = json_encode($temp, JSON_UNESCAPED_UNICODE);

        $optask = OpTask::getById($optaskid);

        $optlog = OpTaskService::addOptLog($optask, $content, $myauditor->id, $followtype, $jsoncontent);

        if ($plantime) {
            $optask->plantime = $plantime;
        }

        $time_str = $optlog->getCreateDayHi();
        echo "<tr>
                <td class='gray f12'> {$time_str} {$optlog->auditor->name}</td>
                <td>{$optlog->content}</td>
              </tr>";

        return self::BLANK;
    }
}
