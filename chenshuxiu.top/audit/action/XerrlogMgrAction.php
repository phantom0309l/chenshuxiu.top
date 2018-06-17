<?php

// XerrlogMgrAction
class XerrlogMgrAction extends AuditBaseAction
{

    // 错误日志列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 10);
        $pagenum = XRequest::getValue("pagenum", 1);
        $level = XRequest::getValue('level', 'all');
        $status = XRequest::getValue('status', 0);

        $cond = '';
        $bind = [];

        if ($level && $level != 'all') {
            $cond .= " and level = :level ";
            $bind[':level'] = $level;
        }

        if ($status >= 0) {
            $cond .= " and status = :status ";
            $bind[':status'] = $status;
        }

        $cond .= " order by id desc ";

        $xerrlogs = Dao::getEntityListByCond4Page('Xerrlog', $pagesize, $pagenum, $cond, $bind, 'xworkdb');

        foreach ($xerrlogs as $a) {
            $a->fixContent();
        }

        XContext::setValue('level', $level);
        XContext::setValue('status', $status);
        XContext::setValue('xerrlogs', $xerrlogs);

        $cnt = Dao::queryValue("select count(*) from xerrlogs where 1=1 $cond", $bind, 'xworkdb');
        $url = "/xerrlogmgr/list?level={$level}&status={$status}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 错误日志详情
    public function doOne () {
        $xerrlogid = XRequest::getValue('xerrlogid', 0);

        $xerrlog = Xerrlog::getById($xerrlogid, 'xworkdb');

        if ($xerrlog->auditorid < 1) {
            $xerrlog->auditorid = $this->myauditor->id;
        }

        XContext::setValue('xerrlog', $xerrlog);

        return self::SUCCESS;
    }

    // 错误日志详情
    public function doModifyPost () {
        $xerrlogid = XRequest::getValue('xerrlogid', 0);
        $status = XRequest::getValue('status', 0);
        $auditorid = XRequest::getValue('auditorid', $this->myauditor->id);
        $remark = XRequest::getValue('remark', '');

        $xerrlog = Xerrlog::getById($xerrlogid, 'xworkdb');

        $xerrlog->auditorid = $auditorid;
        $xerrlog->status = $status;
        $xerrlog->remark = $remark;

        XContext::setJumpPath("/xerrlogmgr/one?xerrlogid={$xerrlogid}");

        return self::SUCCESS;
    }

    // 忽略详情
    public function doIgnoreJson () {
        $xerrlogid = XRequest::getValue('xerrlogid', 0);
        $auditorid = $this->myauditor->id;

        $xerrlog = Xerrlog::getById($xerrlogid, 'xworkdb');

        $xerrlog->auditorid = $auditorid;
        $xerrlog->status = 1;
        $remark = $xerrlog->remark . "\n{$this->myauditor->name}: 忽略";
        $xerrlog->remark = $remark;

        $this->result['data'] = 'success';

        return self::TEXTJSON;
    }
}
