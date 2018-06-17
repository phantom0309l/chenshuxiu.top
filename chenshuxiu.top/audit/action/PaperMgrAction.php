<?php

class PaperMgrAction extends AuditBaseAction
{

    public function dolist () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $papertplid = XRequest::getValue('papertplid', 0);
        $patientid = XRequest::getValue('patientid', 0);

        $papertpls = PaperTplDao::getAllList();

        Xcontext::setValue('papertpls', $papertpls);
        $patient = Patient::getById($patientid);

        $cond = "";
        $bind = [];

        if ($papertplid > 0) {
            $cond .= " and papertplid = :papertplid ";
            $bind[':papertplid'] = $papertplid;
        }

        if ($patient instanceof Patient) {
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patient->id;
        }

        $cond .= " order by id desc ";

        $papers = Dao::getEntityListByCond4Page("Paper", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from papers where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/papermgr/list?patientid={$patientid}&papertplid={$papertplid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("papers", $papers);
        Xcontext::setValue('papertplid', $papertplid);
        Xcontext::setValue('patient', $patient);

        return self::SUCCESS;
    }

    // 获取某个患者的量表列表
    public function doListOfPatient () {
        $patientid = XRequest::getValue('patientid', 0);

        $patient = Patient::getById($patientid);
        $papers = PaperDao::getListByPatientid($patientid);

        XContext::setValue("patient", $patient);
        XContext::setValue("papers", $papers);

        return self::SUCCESS;
    }

    // 获取某个量表模板的量表列表
    public function doListOfPaperTpl () {
        $papertplid = XRequest::getValue('papertplid', 0);

        $paperTpl = PaperTpl::getById($papertplid);

        $papers = PaperDao::getListByPaperTpl($paperTpl);

        XContext::setValue("paperTpl", $paperTpl);
        XContext::setValue("papers", $papers);

        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $papertplid = XRequest::getValue("papertplid", 0);

        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "患者不能为空");

        $papertpl = PaperTpl::getById($papertplid);
        DBC::requireNotEmpty($papertpl, "量表模板不能为空");

        $row = array();
        $row["wxuserid"] = 0;
        $row["userid"] = 0;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $patient->doctorid;
        $row["papertplid"] = $papertplid;
        $row["groupstr"] = $papertpl->groupstr;
        $row["ename"] = $papertpl->ename;
        $row["xanswersheetid"] = 0;
        $row["writer"] = '医助:' . $this->myauditor->name;

        $paper = Paper::createByBiz($row);

        $sheets = XRequest::getValue('sheets', array());

        // 这个值传了也不用
        $myuser = $this->myuser;

        // 真正的目标用户
        XContext::setValue('patient4sheet', $patient);

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, 'Paper', $paper->id);

        $xanswersheetid = $maxXAnswer->xanswersheetid;
        $paper->set4lock("xanswersheetid", $xanswersheetid);

        // 入流
        Pipe::createByEntity($paper, $papertpl->groupstr);

        XContext::setJumpPath("/xanswersheetmgr/modify?xanswersheetid={$xanswersheetid}");

        return self::SUCCESS;
    }

    public function doDetail4OptaskHtml() {
        $paperid = XRequest::getValue('paperid', '');
        DBC::requireNotEmpty($paperid, 'paperid is null');
        $paper = Dao::getEntityById('Paper', $paperid);
        DBC::requireNotEmpty($paper, 'paper is empty');

        XContext::setValue('paper', $paper);
        return self::SUCCESS;
    }
}
