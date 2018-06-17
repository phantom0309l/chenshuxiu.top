<?php

class OpTaskPipeRefMgrAction extends AuditBaseAction
{

    public function doBindOrUnbindJson () {
        $optaskid = XRequest::getValue("optaskid", 0);
        $pipeid = XRequest::getValue("pipeid", 0);
        $status = XRequest::getValue("status", 0);

        $optaskpiperef = OpTaskPipeRefDao::getOneByOptaskidPipeid($optaskid, $pipeid);

        if ($optaskpiperef instanceof OpTaskPipeRef) {
            if ($status == 1) {
                $optaskpiperef->remove();
            }
        } else {
            if ($status == 0) {
                $row = array();
                $row["optaskid"] = $optaskid;
                $row["pipeid"] = $pipeid;
                OpTaskPipeRef::createByBiz($row);
            }
        }
        echo "ok";
        return self::BLANK;
    }
}
