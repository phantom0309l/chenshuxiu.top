<?php

class AuditorDiseaseRefMgrAction extends AuditBaseAction
{

    //给运营绑定分组
    public function doBindDisease () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $auditor = Auditor::getById($auditorid);
        $diseases = DiseaseDao::getListAll();

        XContext::setValue("auditor", $auditor);
        XContext::setValue("diseases", $diseases);
        return self::SUCCESS;
    }

    public function doBindOrUnbindDiseaseJson () {
        $auditorid = XRequest::getValue("auditorid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");
        $status = XRequest::getValue("status", 1);

        $auditordiseaseref = AuditorDiseaseRefDao::getOneByAuditoridDiseaseid($auditorid, $disease->id);

        if( $auditordiseaseref instanceof AuditorDiseaseRef ){
            if($status==0){
                $auditordiseaseref->remove();
            }
        }else{
            if($status==1){
                $row = array();
                $row["auditorid"] = $auditorid;
                $row["diseaseid"] = $disease->id;
                AuditorDiseaseRef::createByBiz($row);
            }
        }
        echo "ok";
        return self::BLANK;
    }
}
