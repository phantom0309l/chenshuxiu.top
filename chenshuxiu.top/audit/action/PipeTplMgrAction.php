<?php

class PipeTplMgrAction extends AuditBaseAction
{

    // 流模板列表
    public function dolist () {
        $pipetpls = Dao::getEntityListByCond('PipeTpl',' order by objtype, objcode ');

        XContext::setValue('pipetpls', $pipetpls);
        return self::SUCCESS;
    }

    // 流模板新建
    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddJson () {
        $title = XRequest::getValue("title", "");
        $show_in_doctor = XRequest::getValue("show_in_doctor", 0);
        $objtype = XRequest::getValue("objtype", "");
        $objcode = XRequest::getValue("objcode", "");
        $content = XRequest::getValue("content", "");

        $row = array();
        $row["title"] = $title;
        $row["show_in_doctor"] = $show_in_doctor;
        $row["objtype"] = $objtype;
        $row["objcode"] = $objcode;
        $row["content"] = $content;

        PipeTpl::createByBiz($row);
        echo "ok";
        return self::blank;
    }

    // 流模板修改
    public function doModify () {
        $pipetplid = XRequest::getValue("pipetplid", 0);

        $pipetpl = PipeTpl::getById($pipetplid);
        DBC::requireTrue($pipetpl instanceof PipeTpl, "不存在:{$pipetpl}");

        XContext::setValue("pipetpl", $pipetpl);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $pipetplid = XRequest::getValue("pipetplid", 0);

        $title = XRequest::getValue("title", "");
        $show_in_doctor = XRequest::getValue("show_in_doctor", 0);
        $content = XRequest::getValue("content", "");

        $pipetpl = PipeTpl::getById($pipetplid);
        DBC::requireTrue($pipetpl instanceof PipeTpl, "不存在:{$pipetpl}");

        $pipetpl->title = $title;
        $pipetpl->show_in_doctor = $show_in_doctor;
        $pipetpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/pipetplmgr/modify?pipetplid=" . $pipetplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
