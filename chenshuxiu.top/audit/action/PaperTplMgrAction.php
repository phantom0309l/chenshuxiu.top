<?php

class PaperTplMgrAction extends AuditBaseAction
{

    public function dolist () {
        $papertplid = XRequest::getValue("papertplid", 0);
        $title = XRequest::getValue("title", '');
        $orderby = XRequest::getValue("orderby", 'id');

        $cond = "";
        $bind = [];

        if ($papertplid > 0) {
            $cond .= " and id = :papertplid ";
            $bind[':papertplid'] = $papertplid;
        }

        if ($title) {
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$title}%";
        }

        if ($orderby == 'id') {
            $cond .= " order by id desc ";
        } elseif ($orderby == 'title') {
            $cond .= " order by title ";
        } elseif ($orderby == 'groupstr') {
            $cond .= " order by groupstr, title ";
        }

        $papertpls = Dao::getEntityListByCond("PaperTpl", $cond, $bind);

        XContext::setValue("papertplid", $papertplid);
        XContext::setValue("title", $title);
        XContext::setValue("orderby", $orderby);

        XContext::setValue("papertpls", $papertpls);
        return self::SUCCESS;
    }

    // 新建
    public function doAdd () {
        $courseid = XRequest::getValue("courseid", 0);
        XContext::setValue("courseid", $courseid);
        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost () {
        $courseid = XRequest::getValue("courseid", 0);
        $title = XRequest::getValue("title", "");
        $groupstr = XRequest::getValue("groupstr", "");
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "brief");
        $content = XRequest::getValue("content", "");
        $diseaseids = XRequest::getValue("diseaseids", []);

        $row = array();
        $row["title"] = $title;
        $row["groupstr"] = $groupstr;
        $row["ename"] = $ename;
        $row["brief"] = $brief;
        $row["content"] = $content;

        $papertpl = PaperTpl::createByBiz($row);
        $course = Course::getById($courseid);

        if ($course instanceof Course) {
            $course->papertplid = $papertpl->id;
        }

        foreach ($diseaseids as $diseaseid) {
            $show_in_audit = XRequest::getValue("show_in_audit");
            $show_in_wx = XRequest::getValue("show_in_wx");
            DiseasePaperTplRef::createByBiz(
                    array(
                        "diseaseid" => $diseaseid,
                        "doctorid" => 0,
                        "papertplid" => $papertpl->id,
                        "show_in_wx" => $show_in_wx == 'on' ? 1 : 0,
                        "show_in_audit" => $show_in_audit == 'on' ? 1 : 0));
        }

        XContext::setJumpPath("/papertplmgr/list");
        return self::SUCCESS;
    }

    // 修改
    public function doModify () {
        $papertplid = XRequest::getValue("papertplid", 0);
        $papertpl = PaperTpl::getById($papertplid);
        XContext::setValue("papertpl", $papertpl);
        return self::SUCCESS;
    }

    // 修改 提交
    public function doModifyPost () {
        $papertplid = XRequest::getValue("papertplid", 0);
        $title = XRequest::getValue("title", "");
        $groupstr = XRequest::getValue("groupstr", "");
        $ename = XRequest::getValue("ename", "");
        $brief = XRequest::getValue("brief", "brief");
        $content = XRequest::getValue("content", "");

        $papertpl = PaperTpl::getById($papertplid);

        $papertpl->title = $title;
        $papertpl->groupstr = $groupstr;
        $papertpl->ename = $ename;
        $papertpl->brief = $brief;
        $papertpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/papertplmgr/modify?papertplid=" . $papertplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 删除量表
    public function doDeletePost () {
        $papertplid = XRequest::getValue("papertplid", 0);

        $papertpl = PaperTpl::getById($papertplid);

        if ($papertpl->xquestionsheetid < 1) {
            $papertpl->remove();
        }

        XContext::setJumpPath("/papertplmgr/list");

        return self::SUCCESS;
    }

    // 获取患者模板及问卷
    public function doOne4patient () {
        $patientid = XRequest::getValue("patientid", 0);
        $papertplid = XRequest::getValue("papertplid", 0);

        $patient = Patient::getById($patientid);
        $papertpl = PaperTpl::getById($papertplid);
        $xquestionsheet = $papertpl->xquestionsheet;

        XContext::setValue("patient", $patient);
        XContext::setValue("papertpl", $papertpl);
        XContext::setValue("xquestionsheet", $xquestionsheet);

        return self::SUCCESS;
    }
}
