<?php

class OpTaskTplAuditorRefMgrAction extends AuditBaseAction
{

    // 给运营绑定分组
    public function doBindOpTaskTpl () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $auditor = Auditor::getById($auditorid);
        $mydisease = $this->mydisease;

        // 全部任务模板
        $optasktpls = OpTaskTplDao::getList();

        if ($mydisease instanceof Disease) {
            $temp = [];
            foreach ($optasktpls as $optasktpl) {
                if ( 0 == $optasktpl->diseaseids || in_array($mydisease->id, $optasktpl->getDiseaseIdArr()) ) {
                    $temp[] = $optasktpl;
                }
            }
            $optasktpls = $temp;
        }

        XContext::setValue("auditor", $auditor);
        XContext::setValue("optasktpls", $optasktpls);
        return self::SUCCESS;
    }

    public function doBindOrUnbindOptasktplJson () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $status = XRequest::getValue("status", 1);

        $optasktplauditorref = OpTaskTplAuditorRefDao::getOneByOptasktplidAuditorid($optasktplid, $auditorid);

        if ($optasktplauditorref instanceof OpTaskTplAuditorRef) {
            if ($status == 0) {
                $optasktplauditorref->remove();
            }
        } else {
            if ($status == 1) {
                $row = array();
                $row["auditorid"] = $auditorid;
                $row["optasktplid"] = $optasktplid;
                OpTaskTplAuditorRef::createByBiz($row);
            }
        }
        echo "ok";
        return self::BLANK;
    }
}
