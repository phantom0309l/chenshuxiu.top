<?php

/**
 * User: lijie
 * Date: 17-12-22
 * Time: 下午7:03
 */
class PipeLevelMgrAction extends AuditBaseAction
{
    public function doFixJson () {
        $pipelevelid = XRequest::getValue('pipelevelid', 0);
        $is_urgent_fix = XRequest::getValue('is_urgent_fix', 0);

        $myauditor = $this->myauditor;

        $pipelevel = PipeLevel::getById($pipelevelid);
        DBC::requireNotEmpty($pipelevel, "pipelevel不能为空");

        $pipelevel->is_urgent_fix = $is_urgent_fix;
        $pipelevel->set4lock('auditorid', $myauditor->id);

        echo "ok";
        return self::BLANK;
    }
}
