<?php

class FaqMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $faqid = XRequest::getValue("faqid", 0);
        $title = XRequest::getValue("title", '');

        $cond = "";
        $bind = [];

        //id筛选
        if($faqid > 0){
            $cond .= " and faqid = :faqid ";
            $bind[":faqid"] = $faqid;
        }

        if('' != $title){
            $cond .= " and title like :title ";
            $bind[":title"] = "%{$title}%";
        }

        //获得实体
        $sql = "select *
                    from faqs
                    where 1 = 1 {$cond} order by id";
        $faqs = Dao::loadEntityList4Page("Faq", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("faqs", $faqs);

        //获得分页
        $countSql = "select count(*)
                    from faqs
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/faqmgr/list?faqid={$faqid}&title={$title}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("faqid", $faqid);
        XContext::setValue("title", $title);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne () {
        $faqid = XRequest::getValue("faqid", 0);

        $faq = Faq::getById($faqid);

        XContext::setValue("faq", $faq);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $auditor = $this->myauditor;

        $row = array();
        $row["id"] = 1 + Dao::queryValue(' select max(id) as maxid from faqs ');
        $row["title"] = $title;
        $row["content"] = $content;

        $faq = Faq::createByBiz($row);

        $row = array();
        $row["faqid"] = $faq->id;
        $row["auditorid"] = $auditor->id;
        $row["content"] = $content;

        FaqLog::createByBiz($row);

        XContext::setJumpPath("/faqmgr/list");
        return self::SUCCESS;
    }

    public function doAddMore () {
        return self::SUCCESS;
    }

    public function doAddMorePost () {
        $titlestr = XRequest::getValue("titlestr", "");
        $contentstr = XRequest::getValue("contentstr", "");
        $auditor = $this->myauditor;

        $title_arr = explode("\n", $titlestr);
        $content_arr = explode("\n", $contentstr);

        if(0==count($title_arr) || 0==count($content_arr)){
            $preMsg = "数量这么少，别在这儿建！！！ " . XDateTime::now();
            XContext::setJumpPath("/faqmgr/addmore?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        if(count($title_arr)!=count($content_arr)){
            $preMsg = "问题数量与答案数量不匹配！！！ " . XDateTime::now();
            XContext::setJumpPath("/faqmgr/addmore?preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }

        foreach ($title_arr as $key => $title) {
            $unitofwork = BeanFinder::get("UnitOfWork");
            $row = array();
            $row["id"] = 1 + Dao::queryValue(' select max(id) as maxid from faqs ');
            $row["title"] = $title;
            $row["content"] = $content_arr[$key];

            $faq = Faq::createByBiz($row);

            $row = array();
            $row["faqid"] = $faq->id;
            $row["auditorid"] = $auditor->id;
            $row["content"] = $faq->content;

            FaqLog::createByBiz($row);
            $unitofwork->commitAndInit();
        }

        XContext::setJumpPath("/faqmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $faqid = XRequest::getValue("faqid", 0);

        $faq = Faq::getById($faqid);
        DBC::requireTrue($faq instanceof Faq, "faq不存在:{$faqid}");
        XContext::setValue("faq", $faq);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $faqid = XRequest::getValue("faqid", 0);
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $auditor = $this->myauditor;

        $faq = Faq::getById($faqid);
        DBC::requireTrue($faq instanceof Faq, "faq不存在:{$faqid}");

        $faq->title = $title;
        $faq->content = $content;

        $row = array();
        $row["faqid"] = $faq->id;
        $row["auditorid"] = $auditor->id;
        $row["content"] = $content;

        FaqLog::createByBiz($row);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/faqmgr/modify?faqid=" . $faqid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
