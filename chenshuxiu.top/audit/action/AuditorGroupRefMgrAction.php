<?php

class AuditorGroupRefMgrAction extends AuditBaseAction
{

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct();
    }

    public function doGetAuditorsByEnameJson() {
        $ename = XRequest::getValue('ename', '');
        $auditorGroupId = XRequest::getValue('auditorgroupid', 0);
        DBC::requireNotEmpty($ename, "ename不能为空");

        $auditorGroupBase = AuditorGroupDao::getByTypeAndEname('base', $ename);
        DBC::requireTrue($auditorGroupBase instanceof AuditorGroup, "没有该ename【{$ename}】的基础配置");
        $auditorArr = [];
        $auditorids = [];

        $auditors = AuditorDao::getListByAuditorGroup($auditorGroupBase);
        foreach ($auditors as $auditor) {
            $auditorArr[$auditor->id] = $auditor->name;
        }

        if($auditorGroupId){
            $auditorGroup = AuditorGroup::getById($auditorGroupId);
            $auditors = AuditorDao::getListByAuditorGroup($auditorGroup);
            foreach ($auditors as $auditor) {
                $auditorArr[$auditor->id] = $auditor->name;
                $auditorids[] = $auditor->id;
            }
        }

        $auditorArr = HtmlCtr::getCheckboxCtrImp($auditorArr, 'auditorids[]', $auditorids , '', 'auditorids');

        $this->result['auditorArr'] = $auditorArr;

        return self::TEXTJSON;
    }
}
