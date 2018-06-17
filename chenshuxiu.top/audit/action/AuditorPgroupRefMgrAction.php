<?php

class AuditorPgroupRefMgrAction extends AuditBaseAction
{

    //给运营绑定分组
    public function doBindPgroup () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $auditor = Auditor::getById($auditorid);
        $pgroups = PgroupDao::getListByDiseaseid(1, " and typestr = 'manage'");

        XContext::setValue("auditor", $auditor);
        XContext::setValue("pgroups", $pgroups);
        return self::SUCCESS;
    }

    public function doBindOrUnbindPgroupJson () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $pgroupid = XRequest::getValue("pgroupid", 0);
        $status = XRequest::getValue("status", 1);

        $auditorpgroupref = AuditorPgroupRefDao::getOneByAuditoridPgroupid($auditorid, $pgroupid);
        if( $auditorpgroupref instanceof AuditorPgroupRef ){
            $auditorpgroupref->status = $status;
        }else{
            $row = array();
            $row["auditorid"] = $auditorid;
            $row["pgroupid"] = $pgroupid;
            $row["status"] = $status;
            AuditorPgroupRef::createByBiz($row);
        }
        echo "ok";
        return self::BLANK;
    }
}
