<?php

class CronProcessVarMgrAction extends AuditBaseAction
{

    public function doAddPost () {
        $cronprocessid = XRequest::getValue("cronprocessid", 0);
        $cronprocesstplvarid = XRequest::getValue("cronprocesstplvarid", 0);
        $value = XRequest::getValue("value", "");

        $cronprocesstplvar = CronProcessTplVar::getById($cronprocesstplvarid);

        $row = array();
        $row['cronprocessid'] = $cronprocessid;
        $row['cronprocesstplvarid'] = $cronprocesstplvarid;
        $row['code'] = $cronprocesstplvar->code;
        $row['value'] = $value;
        $row['unit'] = $cronprocesstplvar->unit;
        $row['remark'] = $cronprocesstplvar->remark;

        $cronprocessvar = CronProcessVar::createByBiz($row);

        XContext::setJumpPath("/cronprocessmgr/list?cronprocessid={$cronprocessid}");
        return self::BLANK;
    }
}
