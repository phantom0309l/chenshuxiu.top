<?php

class OpTaskCheckTplMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }


    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if ($optaskchecktplid > 0) {
            $cond .= " and id = :id ";
            $bind[":id"] = $optaskchecktplid;
        }

        //获得实体
        $sql = "select *
                    from optaskchecktpls
                    where 1 = 1 {$cond} order by id desc";
        $opTaskCheckTpls = Dao::loadEntityList4Page("OpTaskCheckTpl", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("opTaskCheckTpls", $opTaskCheckTpls);

        //获得分页
        $countSql = "select count(*)
                    from optaskchecktpls
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/optaskchecktplmgr/list?optaskchecktplid={$optaskchecktplid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("optaskchecktplid", $optaskchecktplid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne() {
        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);

        $opTaskCheckTpl = OpTaskCheckTpl::getById($optaskchecktplid);

        XContext::setValue("opTaskCheckTpl", $opTaskCheckTpl);
        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddPost() {

        $xquestionsheetid = XRequest::getValue("xquestionsheetid", 0);
        $title = XRequest::getValue("title", '');
        $ename = XRequest::getValue("ename", '');
        $content = XRequest::getValue("content", "");


        $row = array();
        $row["xquestionsheetid"] = $xquestionsheetid;
        $row["title"] = $title;
        $row["ename"] = $title;
        $row["content"] = $content;


        OpTaskCheckTpl::createByBiz($row);

        XContext::setJumpPath("/optaskchecktplmgr/list");
        return self::SUCCESS;
    }

    public function doModify() {
        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);

        $opTaskCheckTpl = OpTaskCheckTpl::getById($optaskchecktplid);
        DBC::requireTrue($opTaskCheckTpl instanceof OpTaskCheckTpl, "opTaskCheckTpl不存在:{$optaskchecktplid}");
        XContext::setValue("opTaskCheckTpl", $opTaskCheckTpl);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost() {
        $optaskchecktplid = XRequest::getValue("optaskchecktplid", 0);
        $xquestionsheetid = XRequest::getValue("xquestionsheetid", 0);
        $title = XRequest::getValue("title", '');
        $ename = XRequest::getValue("ename", '');
        $content = XRequest::getValue("content", "");

        $opTaskCheckTpl = OpTaskCheckTpl::getById($optaskchecktplid);
        DBC::requireTrue($opTaskCheckTpl instanceof OpTaskCheckTpl, "opTaskCheckTpl不存在:{$optaskchecktplid}");

        $opTaskCheckTpl->xquestionsheetid = $xquestionsheetid;
        $opTaskCheckTpl->title = $title;
        $opTaskCheckTpl->ename = $ename;
        $opTaskCheckTpl->content = $content;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/optaskchecktplmgr/modify?optaskchecktplid=" . $optaskchecktplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doOneOpTaskCheckHtml() {
        $auditorid = XRequest::getValue('auditor_id', $this->myauditor->id);
        $optaskCheckid = XRequest::getValue('optaskcheck_id', 0);
        $auditor = Auditor::getById($auditorid);

        if (empty($optaskCheckid)) {
            $startTimeOfWeek = date('Y-m-d H:i:s', strtotime("this week Monday", time()));
            $optaskCheck = OpTaskCheckDao::getFirstByAuditorAndTimeSlot($auditorid, $startTimeOfWeek, date('Y-m-d H:i:s', time()));
        } else {
            $optaskCheck = OpTaskCheck::getById($optaskCheckid);
        }

        if ($optaskCheck instanceof OpTaskCheck) {
            $auditorGroupRef = AuditorGroupRefDao::getByTypeAndAuditorid('base', $auditorid);
            $ename = $auditorGroupRef->auditorgroup->ename;
            $optaskchecktpl = OpTaskCheckTplDao::getByEname($ename);
            XContext::setValue('optaskCheckTpl', $optaskchecktpl);
        }
        // 获取筛选分类
        $arr_filter = PipeTplService::getArrForFilter();

        XContext::setValue("myauditor", $this->myauditor);
        XContext::setValue("mydisease", $this->mydisease);
        XContext::setValue("arr_filter", $arr_filter);
        XContext::setValue('auditor', $auditor);
        XContext::setValue('optaskCheck', $optaskCheck);
        return self::SUCCESS;
    }


    public function doQuestionSheetHtml () {
        $optaskcheckid = XRequest::getValue('optaskcheckid', 0);
        $optaskCheck = OpTaskCheck::getById($optaskcheckid);
        DBC::requireTrue($optaskCheck instanceof OpTaskCheck, "id为 {$optaskcheckid} 的OptaskCheck不存在");

        if($optaskCheck->xanswersheet instanceof  XAnswerSheet){
            // 获取答题列表
            $optaskCheckItems = $optaskCheck->xanswersheet->getAnswers();
        }else {
            // 获取问题列表
            $optaskCheckItems = $optaskCheck->optaskchecktpl->xquestionsheet->getQuestions();
        }

        XContext::setValue('optaskCheck',$optaskCheck);
        XContext::setValue('optaskCheckItems',$optaskCheckItems);

        return self::SUCCESS;
    }
}
        