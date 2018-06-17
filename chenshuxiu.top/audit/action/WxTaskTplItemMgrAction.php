<?php

class WxTaskTplItemMgrAction extends AuditBaseAction
{

    // 微信任务项目列表
    public function dolist () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);
        $wxtasktplitems = WxTaskTplItemDao::getListBy($wxtasktplid);
        XContext::setValue("wxtasktplitems", $wxtasktplitems);
        XContext::setValue("wxtasktplid", $wxtasktplid);
        return self::SUCCESS;
    }

    //  微信任务项目列表
    public function doAdd () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);
        XContext::setValue("wxtasktplid", $wxtasktplid);
        return self::SUCCESS;
    }

    public function doAddPost1 () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);

        for ($i = 6; $i < 22; $i ++) {
            $title = "21天倾听计划 第{$i}天";
            $pos = $i;
            $row = array();
            $row["wxtasktplid"] = $wxtasktplid;
            $row["title"] = $title;
            $row["pos"] = $pos;
            WxTaskTplItem::createByBiz($row);
        }

        XContext::setJumpPath("/wxtasktplitemmgr/list?wxtasktplid={$wxtasktplid}");

        return self::SUCCESS;
    }

    public function doAddPost () {
        $wxtasktplid = XRequest::getValue("wxtasktplid", 0);
        $title = XRequest::getValue("title", "");
        $pos = XRequest::getValue("pos", 0);
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");
        $pictureid = XRequest::getValue("pictureid", 0);

        $row = array();
        $row["wxtasktplid"] = $wxtasktplid;
        $row["title"] = $title;
        $row["pos"] = $pos;
        $row["ename"] = $ename;
        $row["brief"] = $brief;
        $row["content"] = $content;
        $row["pictureid"] = $pictureid;

        WxTaskTplItem::createByBiz($row);
        XContext::setJumpPath("/wxtasktplitemmgr/list?wxtasktplid={$wxtasktplid}");

        return self::SUCCESS;
    }

    //  微信任务项目修改
    public function doModify () {
        $wxtasktplitemid = XRequest::getValue("wxtasktplitemid", 0);
        $wxtasktplitem = WxTaskTplItem::getById($wxtasktplitemid);
        XContext::setValue("wxtasktplitem", $wxtasktplitem);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $wxtasktplitemid = XRequest::getValue("wxtasktplitemid", 0);
        $wxtasktplitem = WxTaskTplItem::getById($wxtasktplitemid);

        $title = XRequest::getValue("title", "");
        $pos = XRequest::getValue("pos", 0);
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "");
        $content = XRequest::getValue("content", "");
        $pictureid = XRequest::getValue("pictureid", "0");
        $picture1id = XRequest::getValue("picture1id", "0");
        $picture2id = XRequest::getValue("picture2id", "0");

        $wxtasktplitem->title = $title;
        $wxtasktplitem->pos = $pos;
        $wxtasktplitem->ename = $ename;
        $wxtasktplitem->brief = $brief;
        $wxtasktplitem->content = $content;
        $wxtasktplitem->pictureid = $pictureid;
        $wxtasktplitem->picture1id = $picture1id;
        $wxtasktplitem->picture2id = $picture2id;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/wxtasktplitemmgr/modify?wxtasktplitemid=" . $wxtasktplitemid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

}
