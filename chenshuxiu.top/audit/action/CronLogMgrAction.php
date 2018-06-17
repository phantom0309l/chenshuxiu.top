<?php

class CronLogMgrAction extends AuditBaseAction
{

    // 列表页
    public function doList () {
        $crontabid = XRequest::getValue("crontabid", 0);
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);

        $cond = '';
        $bind = [];

        if ($crontabid > 0) {
            $cond .= 'and crontabid = :crontabid';
            $bind[':crontabid'] = $crontabid;
        }

        $cond .= ' order by id desc ';

        $cronlogs = Dao::getEntityListByCond4Page('CronLog', $pagesize, $pagenum, $cond, $bind, 'statdb');

        $countSql = "select count(*) as cnt from cronlogs where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind, 'statdb');
        $url = "/cronlogmgr/list?crontabid={$crontabid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("cronlogs", $cronlogs);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne () {
        $cronlogid = XRequest::getValue("cronlogid", 0);

        $cronlog = CronLog::getById($cronlogid, 'statdb');

        XContext::setValue("cronlog", $cronlog);
        return self::SUCCESS;
    }
}
