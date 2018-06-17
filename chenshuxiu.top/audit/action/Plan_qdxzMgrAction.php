<?php

class Plan_qdxzMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList4Patient() {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        $papertpl = PaperTplDao::getByEname('dyspnea');

        $cond = "";
        $bind = [];

        DBC::requireNotEmpty($patient, "patient is null");

        $cond .= " and patientid = :patientid ";
        $bind[":patientid"] = $patientid;

        $cond .= " order by plan_date asc ";

        $plan_qdxzs = Dao::getEntityListByCond("Plan_qdxz", $cond, $bind);

        XContext::setValue("plan_qdxzs", $plan_qdxzs);
        XContext::setValue("patient", $patient);
        XContext::setValue("papertpl", $papertpl);

        return self::SUCCESS;
    }
}
        