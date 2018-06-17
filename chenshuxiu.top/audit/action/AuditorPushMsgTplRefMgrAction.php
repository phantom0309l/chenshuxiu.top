<?php

class AuditorPushMsgTplRefMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    public function doBindAuditorPushMsgTpl() {
        $auditorid = XRequest::getValue("auditorid", 1);
        $auditor = Auditor::getById($auditorid);
        $auditorpushmsgtpls = Dao::getEntityListByCond('AuditorPushMsgTpl');

        XContext::setValue("auditor", $auditor);
        XContext::setValue("auditorpushmsgtpls", $auditorpushmsgtpls);
        return self::SUCCESS;
    }

    public function doBindOrUnbindJson () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $auditorpushmsgtplid = XRequest::getValue("auditorpushmsgtplid", 0);
        $can_ops = XRequest::getValue("can_ops", 1);

        $auditorPushMsgTplRef = AuditorPushMsgTplRefDao::getByAuditoridAndPushMsgTplid($auditorid, $auditorpushmsgtplid);
        if( $auditorPushMsgTplRef instanceof AuditorPushMsgTplRef ){
            $auditorPushMsgTplRef->can_ops = $can_ops;
        }else{
            $row = array();
            $row["auditorid"] = $auditorid;
            $row["auditorpushmsgtplid"] = $auditorpushmsgtplid;
            $row["can_ops"] = $can_ops;
            AuditorPushMsgTplRef::createByBiz($row);
        }
        echo "ok";
        return self::BLANK;
    }
}
