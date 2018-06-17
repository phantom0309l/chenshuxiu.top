<?php
// XcityMgrAction
class XcityMgrAction extends AuditBaseAction
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

    public function doGetxcitys () {
        $xprovinceid = XRequest::getValue('xprovinceid', 0);

        $list = JsonAddress::getArrayXcity($xprovinceid);

        $this->result['data'] = $list;
        return self::TEXTJSON;
    }
}
