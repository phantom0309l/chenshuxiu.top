<?php

class CronProcessTplVarMgrAction extends AuditBaseAction
{

    public function doAdd () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);

        $cronprocesstpl = CronProcessTpl::getById($cronprocesstplid);

        XContext::setValue("cronprocesstpl", $cronprocesstpl);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);
        $code = XRequest::getValue("code", '');
        $name = XRequest::getValue("name", '');
        $unit = XRequest::getValue("unit", '');
        $remark = XRequest::getValue("remark", '');

        $row = array();
        $row['cronprocesstplid'] = $cronprocesstplid;
        $row['code'] = $code;
        $row['name'] = $name;
        $row['unit'] = $unit;
        $row['remark'] = $remark;

        $croprocesstplvar = CronProcessTplVar::createByBiz($row);

        XContext::setJumpPath("/cronprocesstplmgr/modify?cronprocesstplid={$cronprocesstplid}");
        return self::BLANK;
    }

    public function doModify () {
        $cronprocesstplvarid = XRequest::getValue("cronprocesstplvarid", 0);

        $cronprocesstplvar = CronProcessTplVar::getById($cronprocesstplvarid);

        XContext::setValue("cronprocesstplvar", $cronprocesstplvar);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $cronprocesstplvarid = XRequest::getValue("cronprocesstplvarid", 0);
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);
        $name = XRequest::getValue("name", "");
        $unit = XRequest::getValue("unit", "");
        $remark = XRequest::getValue("remark", "");

        $cronprocesstplvar = CronProcessTplVar::getById($cronprocesstplvarid);

        $cronprocesstplvar->name = $name;
        $cronprocesstplvar->unit = $unit;
        $cronprocesstplvar->remark = $remark;

        XContext::setJumpPath("/cronprocesstplmgr/modify?cronprocesstplid={$cronprocesstplid}");
        return self::BLANK;
    }

    public function doDeletePost () {
        $cronprocesstplvarid = XRequest::getValue("cronprocesstplvarid", 0);
        $cronprocesstplid = XRequest::getValue("cronprocesstplid", 0);

        $cronprocesstplvar = CronProcessTplVar::getById($cronprocesstplvarid);
        $cronprocesstplvar->remove();

        XContext::setJumpPath("/cronprocesstplmgr/modify?cronprocesstplid={$cronprocesstplid}");
        return self::BLANK;

    }
}

