<?php

class LessonUserRefMgrAction extends AuditBaseAction
{

    //  患者课程关系列表
    public function doList () {

        $lessonid = XRequest::getValue("lessonid", 0);
        $patientid = XRequest::getValue("patientid", 0);
        $userid = XRequest::getValue("userid", 0);
        $wxuserid = XRequest::getValue("wxuserid", 0);

        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        // 指定的
        if ($userid > 0) {
            $lessonuserrefs = LessonUserRefDao::getListByUser4Page($userid, $pagesize, $pagenum);
            $cnt = LessonUserRefDao::getCntOfUser($userid);
            $urlcond = "?userid={$userid}";
        } elseif ($lessonid > 0) {
            $lessonuserrefs = LessonUserRefDao::getListByLesson4Page($lessonid, $pagesize, $pagenum);
            $cnt = LessonUserRefDao::getCntOfLesson($lessonid);
            $urlcond = "?lessonid={$lessonid}";
        } elseif ($patientid > 0) {
            $lessonuserrefs = LessonUserRefDao::getListByPatient4Page($patientid, $pagesize, $pagenum);
            $cnt = LessonUserRefDao::getCntOfPatient($patientid);
            $urlcond = "?patientid={$patientid}";
        } else {
            $lessonuserrefs = Dao::getEntityListByCond4Page("LessonUserRef", $pagesize, $pagenum);
            $cnt = LessonUserRefDao::getAllCnt();
            $urlcond = "";
        }

        // 翻页begin
        $url = "/lessonuserrefmgr/list/$urlcond";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue("lessonuserrefs", $lessonuserrefs);

        return self::SUCCESS;
    }

    //  患者课程关系详情
    public function doOne () {
        $lessonuserrefid = XRequest::getValue("lessonuserrefid", 0);
        $lessonUserRef = LessonUserRef::getById($lessonuserrefid);

        XContext::setValue("lessonUserRef", $lessonUserRef);

        return self::SUCCESS;
    }

    public function doOneHtml () {
        $lessonuserrefid = XRequest::getValue("lessonuserrefid", 0);
        $lessonUserRef = LessonUserRef::getById($lessonuserrefid);

        XContext::setValue("lessonUserRef", $lessonUserRef);

        return self::SUCCESS;
    }
}
