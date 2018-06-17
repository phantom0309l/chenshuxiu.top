<?php
// CronTabMgrAction
class CronTabMgrAction extends AuditBaseAction
{

    public function doList () {
        $cond = 'order by `when` desc, lastcrontime asc';
        $crontabs = Dao::getEntityListByCond('CronTab', $cond, []);

        XContext::setValue("crontabs", $crontabs);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $process_name = XRequest::getValue("process_name", "");
        $type = XRequest::getValue("type", "");
        $when = XRequest::getValue("when", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $filepath = XRequest::getValue("filepath", "");

        $row = array();
        $row["id"] = 1 + Dao::queryValue('select max(id) as maxid from crontabs');
        $row["process_name"] = $process_name;
        $row["type"] = $type;
        $row["when"] = $when;
        $row["title"] = $title;
        $row["content"] = $content;
        $row["filepath"] = $filepath;

        CronTab::createByBiz($row);

        XContext::setJumpPath("/crontabmgr/list");

        return self::BLANK;
    }

    public function doModify () {
        $crontabid = XRequest::getValue("crontabid", 0);

        $crontab = CronTab::getById($crontabid);

        XContext::setValue("crontab", $crontab);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $crontabid = XRequest::getValue("crontabid", 0);
        $process_name = XRequest::getValue("process_name", "");
        $type = XRequest::getValue("type", "");
        $when = XRequest::getValue("when", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $filepath = XRequest::getValue("filepath", "");
        $status = XRequest::getValue("status", 1);

        $crontab = CronTab::getById($crontabid);
        $crontab->process_name = $process_name;
        $crontab->type = $type;
        $crontab->when = $when;
        $crontab->title = $title;
        $crontab->content = $content;
        $crontab->filepath = $filepath;
        $crontab->status = $status;

        XContext::setJumpPath("/crontabmgr/modify?crontabid={$crontabid}&preMsg=" . urlencode('修改已保存'));
        return self::BLANK;
    }
}
