<?php
// 答卷管理
class XAnswerSheetMgrAction extends AuditBaseAction
{

    // 答卷列表的优化显示模式
    public function doList2 () {
        $this->doList();
        return self::SUCCESS;
    }

    // 问卷列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $xquestionsheetid = XRequest::getValue("xquestionsheetid", 0);
        $patientid = XRequest::getValue("patientid", 0);
        $cond = "";
        $bind = [];
        if ($xquestionsheetid > 0) {
            $cond = ' and xquestionsheetid=:xquestionsheetid ';
            $bind[':xquestionsheetid'] = $xquestionsheetid;
        }

        if ($patientid > 0) {
            $cond .= ' and patientid=:patientid ';
            $bind[':patientid'] = $patientid;

            $thepatient = Patient::getById($patientid);
            XContext::setValue('thepatient', $thepatient);
        }

        XContext::setValue('xquestionsheetid', $xquestionsheetid);

        $xAnswerSheets = Dao::getEntityListByCond4Page('XAnswerSheet', $pagesize, $pagenum, "$cond order by id desc ", $bind);

        $cnt = Dao::queryValue("select count(*) from xanswersheets where 1=1 $cond", $bind);

        $url = "/xanswersheetmgr/list?xquestionsheetid={$xquestionsheetid}&patientid={$patientid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('list', $xAnswerSheets);

        return self::SUCCESS;
    }

    // 提交整张答卷
    public function doAnswersPost () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $sheets = XRequest::getValue('sheets', array());
        // print_r($sheets);
        // exit;
        $myuser = $this->myuser;

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);

        $preMsg = '从创建过来的';
        XContext::setJumpPath("/xanswersheetmgr/modify?xanswersheetid={$maxXAnswer->xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 提交整张答卷
    public function doAnswersPostForCheckup () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $url = XRequest::getValue('url', '');

        $sheets = XRequest::getValue('sheets', array());
        $myuser = $this->myuser;

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);

        $preMsg = '从创建过来的';
        XContext::setJumpPath($url . "&xanswersheetid={$maxXAnswer->xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 提交整张答卷, 应该是修数据用的
    public function doAnswersPostFix () {
        $useid = XRequest::getValue('userid', 0);
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $createtime = XRequest::getValue('readtime', "");
        $sheets = XRequest::getValue('sheets', array());

        $user = User::getById($useid);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
        $lesson = Lesson::getById($xquestionsheet->objid);

        $doctorid = 0;
        if ($user->patient instanceof Patient) {
            // done pcard fix
            $doctorid = $user->patient->doctorid;
        }

        $wxuserid = $user->getWxUserIdIfOnlyOne();

        $lessonUserRef = $lesson->getLessonUserRef($user);
        if (false == $lessonUserRef instanceof LessonUserRef) {
            $row = array();
            $row["createtime"] = $createtime;
            $row["wxuserid"] = $wxuserid;
            $row["userid"] = $user->id;
            $row["patientid"] = $user->patientid;
            $row["doctorid"] = $doctorid;
            $row["lessonid"] = $lesson->id;
            $row["viewcnt"] = 1;
            $row["readtime"] = $createtime;
            $lessonUserRef = LessonUserRef::createByBiz($row);
            Pipe::createByEntity($lessonUserRef);
        }

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $user, $lessonUserRef->getClassName(), $lessonUserRef->id);
        $sheet = $maxXAnswer->xanswersheet;
        $sheet->set4lock("createtime", $createtime);
        $sheet->set4lock("wxuserid", $wxuserid);

        $preMsg = '从创建过来的';
        XContext::setJumpPath("/xanswersheetmgr/modify?xanswersheetid={$maxXAnswer->xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 提交第一题的答案
    public function doFirstAnswerPost () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $sheets = XRequest::getValue('sheets', array());
        $myuser = $this->myuser;

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);

        $preMsg = '从创建过来的';
        XContext::setJumpPath("/xanswersheetmgr/nextquestion?xanswersheetid={$maxXAnswer->xanswersheetid}&prepos={$maxXAnswer->pos}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 下一题
    public function doNextQuestion () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $prepos = XRequest::getValue('prepos', 0);

        $xanswersheet = XAnswerSheet::getById($xanswersheetid);

        XContext::setValue('xanswersheet', $xanswersheet);
        XContext::setValue('prepos', $prepos);

        if ($prepos >= $xanswersheet->getMaxQuestionPos()) {
            $preMsg = "题都做完了,准备修改";
            XContext::setJumpPath("/xanswersheetmgr/modify?xanswersheetid={$xanswersheetid}&preMsg={$preMsg}");
        }

        return self::SUCCESS;
    }

    // 下一题
    public function doNextQuestionPost () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $sheets = XRequest::getValue('sheets', array());
        $myuser = $this->myuser;

        // 提交答卷,新建
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);

        $preMsg = '继续做下一题';
        XContext::setJumpPath("/xanswersheetmgr/nextquestion?xanswersheetid={$maxXAnswer->xanswersheetid}&prepos={$maxXAnswer->pos}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 答卷修改
    public function doModify () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);

        XContext::setValue('xanswersheet', $xanswersheet);
        return self::SUCCESS;
    }

    // 便于打印的格式
    public function doPrint () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);

        XContext::setValue('xanswersheet', $xanswersheet);
        return self::SUCCESS;
    }

    // 答卷修改 提交
    public function doModifyPost () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);

        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        $sheets = XRequest::getValue('sheets', array());
        $myuser = $this->myuser;

        // 提交答卷,修改
        $maxXAnswer = XWendaService::doPost($sheets, $myuser, $objtype, $objid);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/xanswersheetmgr/modify?xanswersheetid={$xanswersheetid}&preMsg={$preMsg}");

        return self::SUCCESS;
    }

    // 删除答卷
    public function doDeleteJson () {
        $xanswersheetid = XRequest::getValue('xanswersheetid', 0);
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);

        $xanswers = $xanswersheet->getAnswers();
        foreach ($xanswers as $a) {
            $a->remove();
        }
        $xanswersheet->remove();

        echo 'success';
        return self::BLANK;
    }

    // 删除答案
    public function doDeleteXanswerJson () {
        $xanswerid = XRequest::getValue('xanswerid', 0);
        $xanswer = XAnswer::getById($xanswerid);

        $xansweroptionrefs = $xanswer->getXAnswerOptionRefs();
        foreach ($xansweroptionrefs as $a) {
            $a->remove();
        }

        $xanswer->remove();
        echo 'success';

        return self::BLANK;
    }
}
