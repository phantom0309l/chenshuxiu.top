<?php

class PatientRecordTplMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $patientrecordtplid = XRequest::getValue("patientrecordtplid", 0);
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($patientrecordtplid > 0){
            $cond .= " and id = :patientrecordtplid ";
            $bind[":id"] = $patientrecordtplid;
        }

        //疾病组筛选
        if($diseasegroupid > 0){
            $cond .= " and diseasegroupid = :diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        //获得实体
        $sql = "select *
                    from patientrecordtpls
                    where 1 = 1 {$cond} order by id desc";
        $patientRecordTpls = Dao::loadEntityList4Page("PatientRecordTpl", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("patientRecordTpls", $patientRecordTpls);

        //获得分页
        $countSql = "select count(*)
                    from patientrecordtpls
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/patientrecordtplmgr/list?patientrecordtplid={$patientrecordtplid}&diseasegroupid={$diseasegroupid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("patientrecordtplid", $patientrecordtplid);
        XContext::setValue("diseasegroupid", $diseasegroupid);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {

        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $ename = XRequest::getValue("ename", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $style_class = XRequest::getValue("style_class", "");

        DBC::requireTrue($diseasegroupid > 0, "疾病组不能为空");

        $diseaseGroup = DiseaseGroup::getById($diseasegroupid);
        $patientRecordTpls = PatientRecordTplDao::getListByDiseaseGroup($diseaseGroup);

        $row = array();
        $row["diseasegroupid"] = $diseasegroupid;
        $row["ename"] = $ename;
        $row["title"] = $title;
        $row["content"] = $content;
        $row["pos"] = count($patientRecordTpls) + 1;
        $row["is_show"] = 1;
        $row["style_class"] = $style_class;

        PatientRecordTpl::createByBiz($row);

        XContext::setJumpPath("/patientrecordtplmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $patientrecordtplid = XRequest::getValue("patientrecordtplid", 0);

        $patientRecordTpl = PatientRecordTpl::getById($patientrecordtplid);
        DBC::requireTrue($patientRecordTpl instanceof PatientRecordTpl, "patientRecordTpl不存在:{$patientrecordtplid}");
        XContext::setValue("patientRecordTpl", $patientRecordTpl);
        XContext::setValue("diseasegroupid", $patientRecordTpl->diseasegroupid);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $patientrecordtplid = XRequest::getValue("patientrecordtplid", 0);
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $ename = XRequest::getValue("ename", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $is_show = XRequest::getValue("is_show", 1);
        $style_class = XRequest::getValue("style_class", "");

        $patientRecordTpl = PatientRecordTpl::getById($patientrecordtplid);
        DBC::requireTrue($patientRecordTpl instanceof PatientRecordTpl, "patientRecordTpl不存在:{$patientrecordtplid}");

        DBC::requireTrue($diseasegroupid > 0, "疾病组不能为空");

        $patientRecordTpl->diseasegroupid = $diseasegroupid;
        $patientRecordTpl->ename = $ename;
        $patientRecordTpl->title = $title;
        $patientRecordTpl->content = $content;
        $patientRecordTpl->is_show = $is_show;
        $patientRecordTpl->style_class = $style_class;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/patientrecordtplmgr/modify?patientrecordtplid=" . $patientrecordtplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
