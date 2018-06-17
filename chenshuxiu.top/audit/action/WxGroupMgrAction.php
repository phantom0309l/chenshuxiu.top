<?php

class WxGroupMgrAction extends AuditBaseAction
{

    //列表
    public function doList () {
        $wxgroups = WxGroupDao::getAllList();
        XContext::setValue("wxgroups", $wxgroups);
        return self::SUCCESS;
    }

    //新建
    public function doAdd () {
        return self::SUCCESS;
    }

    //新建提交
    public function doAddPost () {
        $wxshopid = XRequest::getValue("wxshopid", 0);
        $groupid = XRequest::getValue("groupid", "");
        $ename = XRequest::getValue("ename", "");
        $name = XRequest::getValue("name", "");
        $content = XRequest::getValue("content", "");

        $row = array();
        $row["wxshopid"] = $wxshopid;
        $row["groupid"] = $groupid;
        $row["ename"] = $ename;
        $row["name"] = $name;
        $row["content"] = $content;

        WxGroup::createByBiz($row);
        XContext::setJumpPath("/wxgroupmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $wxgroupid = XRequest::getValue("wxgroupid", 0);

        $wxgroup = WxGroup::getById($wxgroupid);
        XContext::setValue("wxgroup", $wxgroup);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $wxgroupid = XRequest::getValue("wxgroupid", 0);
        $wxshopid = XRequest::getValue("wxshopid", 0);
        $groupid = XRequest::getValue("groupid", "");
        $ename = XRequest::getValue("ename", "");
        $name = XRequest::getValue("name", "");
        $content = XRequest::getValue("content", "");

        $wxgroup = WxGroup::getById($wxgroupid);

        $wxgroup->wxshopid = $wxshopid;
        $wxgroup->groupid = $groupid;
        $wxgroup->ename = $ename;
        $wxgroup->name = $name;
        $wxgroup->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/wxgroupmgr/modify?wxgroupid=" . $wxgroupid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
