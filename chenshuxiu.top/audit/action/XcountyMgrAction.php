<?php
// XquMgrAction
class XcountyMgrAction extends AuditBaseAction
{

    public function doList () {
        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }

    public function doGetxcountys () {
        $xcityid = XRequest::getValue('xcityid', 0);

        $list = JsonAddress::getArrayXcounty($xcityid);

        $this->result['data'] = $list;
        return self::TEXTJSON;
    }
}
