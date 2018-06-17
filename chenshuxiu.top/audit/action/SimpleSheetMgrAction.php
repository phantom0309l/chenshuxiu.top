<?php
// SimpleSheetMgrAction
class SimpleSheetMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagesize = XRequest::getValue('pagesize', 100);

        $simplesheettplid = XRequest::getValue('simplesheettplid', 0);
        $patientid = XRequest::getValue('patientid', 0);

        $cond = "";
        $bind = [];

        if ($simplesheettplid) {
            $cond .= " and simplesheettplid = :simplesheettplid ";
            $bind[':simplesheettplid'] = $simplesheettplid;
        }

        if ($patientid) {
            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        $simplesheets = Dao::getEntityListByCond4Page('SimpleSheet', $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) from simplesheets where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/simplesheetmgr/list?simplesheettplid={$simplesheettplid}&patientid={$patientid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('simplesheets', $simplesheets);
        XContext::setValue('simplesheettplid', $simplesheettplid);
        XContext::setValue('patientid', $patientid);

        return self::SUCCESS;
    }

    public function doOneShow () {
        $simplesheetid = XRequest::getValue('simplesheetid', 0);
        $simplesheet = SimpleSheet::getById($simplesheetid);

        XContext::setValue('simplesheet', $simplesheet);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $simplesheettplid = XRequest::getValue('simplesheettplid', 0);
        $content = XRequest::getValue('content', 0);

        $row = [];
        $row["patientid"] = $patientid;
        $row["simplesheettplid"] = $simplesheettplid;
        $row["content"] = json_encode($content, JSON_UNESCAPED_UNICODE);
        $simpleshee = SimpleSheet::createByBiz($row);

        XContext::setJumpPath('/simplesheettplmgr/list');

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $simplesheetid = XRequest::getValue('simplesheetid', 0);
        $simplesheet = SimpleSheet::getById($simplesheetid);
        $content = XRequest::getValue('content', 0);

        $simplesheet->content = json_encode($content, JSON_UNESCAPED_UNICODE);

        XContext::setJumpPath('/simplesheetmgr/oneshow?simplesheetid=' . $simplesheetid);

        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }
}
