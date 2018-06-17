<?php

class DoctorServiceOrderTplMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorserviceordertplid = XRequest::getValue("doctorserviceordertplid", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($doctorserviceordertplid > 0){
            $cond .= " and doctorserviceordertplid = :doctorserviceordertplid ";
            $bind[":doctorserviceordertplid"] = $doctorserviceordertplid;
        }

        //获得实体
        $sql = "select *
                    from doctorserviceordertpls
                    where 1 = 1 {$cond} order by id desc";
        $doctorServiceOrderTpls = Dao::loadEntityList4Page("DoctorServiceOrderTpl", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctorServiceOrderTpls", $doctorServiceOrderTpls);

        //获得分页
        $countSql = "select count(*)
                    from doctorserviceordertpls
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorserviceordertplmgr/list?doctorserviceordertplid={$doctorserviceordertplid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("doctorserviceordertplid", $doctorserviceordertplid);
        return self::SUCCESS;
    }

    // 详情页
    public function doOne () {
        $doctorserviceordertplid = XRequest::getValue("doctorserviceordertplid", 0);

        $doctorServiceOrderTpl = DoctorServiceOrderTpl::getById($doctorserviceordertplid);

        XContext::setValue("doctorServiceOrderTpl", $doctorServiceOrderTpl);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {

        $ename = XRequest::getValue("ename", "");
        DBC::requireTrue($ename != "", "ename不能为空");
        $title = XRequest::getValue("title", "");
        DBC::requireTrue($title != "", "title不能为空");
        $content = XRequest::getValue("content", "");
        $price = XRequest::getValue("price", 0);

        $row = array();
        $row["ename"] = $ename;
        $row["title"] = $title;
        $row["content"] = $content;
        $row["price"] = $price*100;

        DoctorServiceOrderTpl::createByBiz($row);

        XContext::setJumpPath("/doctorserviceordertplmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $doctorserviceordertplid = XRequest::getValue("doctorserviceordertplid", 0);

        $doctorServiceOrderTpl = DoctorServiceOrderTpl::getById($doctorserviceordertplid);
        DBC::requireTrue($doctorServiceOrderTpl instanceof DoctorServiceOrderTpl, "doctorServiceOrderTpl不存在:{$doctorserviceordertplid}");
        XContext::setValue("doctorServiceOrderTpl", $doctorServiceOrderTpl);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $doctorserviceordertplid = XRequest::getValue("doctorserviceordertplid", 0);
        $ename = XRequest::getValue("ename", "");
        DBC::requireTrue($ename != "", "ename不能为空");
        $title = XRequest::getValue("title", "");
        DBC::requireTrue($title != "", "title不能为空");
        $content = XRequest::getValue("content", "");
        $price = XRequest::getValue("price", 0);

        $doctorServiceOrderTpl = DoctorServiceOrderTpl::getById($doctorserviceordertplid);
        DBC::requireTrue($doctorServiceOrderTpl instanceof DoctorServiceOrderTpl, "doctorServiceOrderTpl不存在:{$doctorserviceordertplid}");

        $doctorServiceOrderTpl->ename = $ename;
        $doctorServiceOrderTpl->title = $title;
        $doctorServiceOrderTpl->content = $content;
        $doctorServiceOrderTpl->price = $price*100;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/doctorserviceordertplmgr/modify?doctorserviceordertplid=" . $doctorserviceordertplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
