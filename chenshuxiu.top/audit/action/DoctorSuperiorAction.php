<?php

class DoctorSuperiorMgrAction extends AuditBaseAction {
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);
        $cond = ' ORDER BY superior_doctorid ';
        $bind = [];
        $doctorSuperiors = Dao::getEntityListByCond4Page("Doctor_Superior", $pagesize, $pagenum, $cond, $bind);
        $countSql = "SELECT COUNT(*) AS cnt FROM doctor_superiors WHERE 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorsuperiormgr/list";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('doctorSuperiors', $doctorSuperiors);
        XContext::setValue('pagelink', $pagelink);

        return self::SUCCESS;
    }

    public function doDelete() {
        $doctorSuperiorId = XRequest::getValue('doctor_superiorid', '');
        $doctorSuperior = Doctor_Superior::getById($doctorSuperiorId);
        DBC::requireNotEmpty($doctorSuperior, 'doctor_superiorid is null');

        $doctorSuperior->remove();
        $preMsg = '删除成功';
        XContext::setJumpPath("/doctorsuperiormgr/list?preMsg=" . urlencode($preMsg));

        return TEXTJSON;
    }
}
