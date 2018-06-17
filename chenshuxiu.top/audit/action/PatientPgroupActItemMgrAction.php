<?php

class PatientPgroupActItemMgrAction extends AuditBaseAction
{

    public function doReplyMsgJson () {
        $patientid = XRequest::getValue("patientid", 0);
        $content = XRequest::getValue("content", "");
        $patientpgroupactitemid = XRequest::getValue("patientpgroupactitemid", 0);
        $isnote = XRequest::getValue("isnote", 0);
        $myauditor = $this->myauditor;

        $patientpgroupactitem = PatientPgroupActItem::getById($patientpgroupactitemid);

        $wxuser = $patientpgroupactitem->wxuser;

        if($wxuser instanceof WxUser){
            $pushmsg = PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
            if ($pushmsg instanceof PushMsg) {
                $patientpgroupactitem->set4lock("pushmsgid", $pushmsg->id);
            }
            if ($isnote) {
                $row = array();
                $row['content'] = $content;
                $row['objtype'] = $patientpgroupactitem->objtype;
                $row['objid'] = $patientpgroupactitem->objid;
                $row['typestr'] = "auditorNote";
                Comment::createByBiz($row);
            }
        }

        echo "ok";
        return self::BLANK;
    }

    public function doModifyIsokJson () {
        $isok = XRequest::getValue("isok", 0);
        $patientpgroupactitemid = XRequest::getValue("patientpgroupactitemid", 0);

        $patientpgroupactitem = PatientPgroupActItem::getById($patientpgroupactitemid);

        $patientpgroupactitem->isok = $isok;

        echo "ok";
        return self::BLANK;
    }

}
