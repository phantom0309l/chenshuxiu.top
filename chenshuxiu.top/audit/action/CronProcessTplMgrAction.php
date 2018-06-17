<?php

class CronProcessTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $cronprocesstpls = CronProcessTplDao::getAllList();

        XContext::setValue("cronprocesstpls", $cronprocesstpls);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $tasktype = XRequest::getValue("tasktype", '');
        $title = XRequest::getValue("title", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $content = XRequest::getValue("content", '');

        $row = array();
        $row['tasktype'] = $tasktype;
        $row['title'] = $title;
        $row['groupstr'] = $groupstr;
        $row['content'] = $content;

        $cronprocesstpl = CronProcessTpl::createByBiz($row);

        XContext::setJumpPath("/cronprocesstplmgr/modify?cronprocesstplid={$cronprocesstpl->id}");
        return self::BLANK;
    }

    public function doModify () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);

        $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);

        $cronprocesstplvars = CronProcessTplVarDao::getListByCronprocesstplid($cronprocesstplid);

        XContext::setValue("cronprocesstpl", $cronprocesstpl);
        XContext::setValue("cronprocesstplvars", $cronprocesstplvars);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);
        $title = XRequest::getValue("title", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $content = XRequest::getValue("content", '');

        $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);

        $cronprocesstpl->title = $title;
        $cronprocesstpl->groupstr = $groupstr;
        $cronprocesstpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/cronprocesstplmgr/modify?cronprocesstplid=" . $cronprocesstplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
