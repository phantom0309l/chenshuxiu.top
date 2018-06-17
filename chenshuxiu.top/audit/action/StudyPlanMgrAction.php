<?php

class StudyPlanMgrAction extends AuditBaseAction
{
    public function doReplyMsgJson () {
        $patientpgrouprefid = XRequest::getValue("patientpgrouprefid", 0);
        $content = XRequest::getValue("content", "");
        $myauditor = $this->myauditor;

        $patientpgroupref = PatientPgroupRef::getById($patientpgrouprefid);

        $wxuser = $patientpgroupref->wxuser;
        $patient = $patientpgroupref->patient;

        if($wxuser instanceof WxUser){
            PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
        }elseif( $patient instanceof Patient ){
            $pcard = $patient->getMasterPcard();
            PushMsgService::sendTxtMsgToWxUsersOfPcardByAuditor($pcard, $myauditor, $content);
        }

        echo "ok";
        return self::BLANK;
    }

}
