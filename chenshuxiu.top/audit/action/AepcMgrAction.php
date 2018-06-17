<?php
// 答卷管理
class AepcMgrAction extends AuditBaseAction
{
    // 问卷列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $patientid = XRequest::getValue("patientid", 0);
        $cond = " and papertplid in (275143816, 275209326, 312586776)";
        $bind = [];

        $thepatient = null;
        if ($patientid > 0) {
            $cond .= ' and patientid=:patientid ';
            $bind[':patientid'] = $patientid;

            $thepatient = Patient::getById($patientid);
        }
        XContext::setValue('thepatient', $thepatient);

        $papers = Dao::getEntityListByCond4Page('Paper', $pagesize, $pagenum, "$cond order by id desc ", $bind);

        $cnt = Dao::queryValue("select count(*) from papers where 1=1 $cond", $bind);

        $url = "/aepcmgr/list?patientid={$patientid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('papers', $papers);

        return self::SUCCESS;
    }

    // 提交
    public function doAdd () {
        $papertplid = XRequest::getValue('papertplid', 0);
        $thepatientid = XRequest::getValue('thepatientid', 0);
        $papertpl = PaperTpl::getById($papertplid);

        $paperid = XRequest::getValue('paperid', 0);
        $paper = Paper::getById($paperid);

        $issimple = XRequest::getValue('issimple', 0);
        $xquestionsheet = $papertpl->xquestionsheet;

        XContext::setValue('xquestionsheet', $xquestionsheet);
        XContext::setValue('papertpl', $papertpl);
        XContext::setValue('thepatientid', $thepatientid);
        XContext::setValue('paper', $paper);
        return self::SUCCESS;
    }

    // 提交整张答卷
    public function doAddPost () {
        $papertplid = XRequest::getValue("papertplid", 0);
        $patientid = XRequest::getValue("thepatientid", 0);

        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "患者不能为空");

        $papertpl = PaperTpl::getById($papertplid);
        DBC::requireNotEmpty($papertpl, "量表模板不能为空");

        $row = array();
        $row["wxuserid"] = 0;
        $row["userid"] = 0;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $patient->doctorid; // done pcard fix
        $row["papertplid"] = $papertplid;
        $row["groupstr"] = $papertpl->groupstr;
        $row["ename"] = $papertpl->ename;
        $row["xanswersheetid"] = 0;
        $row["writer"] = '医助:' . $this->myauditor->name;

        $paper = Paper::createByBiz($row);

        $sheets = XRequest::getValue('sheets', array());
        $myuser = $this->myuser;
        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, 'Paper', $paper->id);

        $xanswersheetid = $maxXAnswer->xanswersheetid;
        $paper->set4lock("xanswersheetid", $xanswersheetid);

        $preMsg = '从创建过来的';
        XContext::setJumpPath("/aepcmgr/modify?xanswersheetid={$maxXAnswer->xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 创建跟踪报告成功
    public function doAddAEPCPost () {
        $papertplid = XRequest::getValue("papertplid", 0);
        $patientid = XRequest::getValue("thepatientid", 0);
        $paperid = XRequest::getValue("paperid", 0);

        $paper = Paper::getById($paperid);
        DBC::requireNotEmpty($paper, "量表不能为空");

        $paperNew = $paper->copyOne($this->myauditor);

        $papertplid_new = $paperNew->papertplid;
        if($papertplid_new != $papertplid){
            $papertpl = PaperTpl::getById($papertplid);
            $paperNew->set4lock("papertplid", $papertpl->id);
            $paperNew->groupstr = $papertpl->groupstr;
            $paperNew->ename = $papertpl->ename;
        }

        $preMsg = '创建跟踪报告成功';
        XContext::setJumpPath("/aepcmgr/modify?xanswersheetid={$paperNew->xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 答卷修改
    public function doModify () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);

        XContext::setValue('xanswersheet', $xanswersheet);
        return self::SUCCESS;
    }

    // 答卷修改 提交
    public function doModifyPost () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $sheets = XRequest::getValue('sheets', array());

        // 提交答卷,修改
        $maxXAnswer = XWendaService::doModifyAll($sheets);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/aepcmgr/modify?xanswersheetid={$xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    public function doAePrint(){
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);
        XContext::setValue('xanswersheetid', $xanswersheetid);

        $event_no = AepcService::getEventNo($xanswersheetid);
        XContext::setValue('event_no', $event_no);
        return self::SUCCESS;
    }

    public function doAePrinttest(){
        return self::SUCCESS;
    }

    public function doPcPrint(){
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);
        XContext::setValue('xanswersheetid', $xanswersheetid);

        return self::SUCCESS;
    }

    public function doOutPutPDF () {
        $paperid = XRequest::getValue('paperid', 0);
        $paper = Paper::getById($paperid);
        $xanswersheetid = $paper->xanswersheetid;
        $title = AepcService::genPDFTitle($paper);

        require_once (ROOT_TOP_PATH . "/../core/tools/dompdf/autoload.inc.php");
        // instantiate and use the dompdf class
        $img_uri = Config::getConfig("img_uri");
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);

        $type = XRequest::getValue('type', "ae");
        $www_uri = Config::getConfig("www_uri");
        if($type == 'ae'){
            $url = "{$www_uri}/show/aeprint?xanswersheetid={$xanswersheetid}";
            //$url = ROOT_TOP_PATH . "/audit/tpl/aepcmgr/aeprinttest.tpl.php";
        }else{
            $url = "{$www_uri}/show/pcprint?xanswersheetid={$xanswersheetid}";
        }
        $html = file_get_contents($url);
        Debug::$xunitofwork_create_close = true;
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation portrait or landscape
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($title, array("Attachment" => true));
        exit();

        return self::SUCCESS;
    }

    // AEPC量表删除Json
    public function doDeleteJson () {
        $paperid = XRequest::getValue('paperid', 0);

        $paper = Paper::getById($paperid);
        $paper->remove();

        echo "ok";
        return self::BLANK;
    }
}
