<?php

class WxTaskTplMgrAction extends AuditBaseAction
{

    public function dolist () {
        $wxtasktpls = WxTaskTplDao::getAll();
        XContext::setValue("wxtasktpls", $wxtasktpls);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");
        $pictureid = XRequest::getValue("pictureid", "0");

        $row = array();
        $row["title"] = $title;
        $row["ename"] = $ename;
        $row["brief"] = $brief;
        $row["content"] = $content;
        $row["pictureid"] = $pictureid;

        WxTaskTpl::createByBiz($row);
        XContext::setJumpPath("/wxtasktplmgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);
        $wxtasktpl = WxTaskTpl::getById($wxtasktplid);
        XContext::setValue("wxtasktpl", $wxtasktpl);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);
        $wxtasktpl = WxTaskTpl::getById($wxtasktplid);

        $title = XRequest::getValue("title", "");
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");
        $pictureid = XRequest::getValue("pictureid", "0");

        $wxtasktpl->title = $title;
        $wxtasktpl->ename = $ename;
        $wxtasktpl->brief = $brief;
        $wxtasktpl->content = $content;
        $wxtasktpl->pictureid = $pictureid;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/wxtasktplmgr/modify?wxtasktplid=" . $wxtasktplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

}

