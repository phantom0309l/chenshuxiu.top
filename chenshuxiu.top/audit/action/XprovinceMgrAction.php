<?php
// XprovinceMgrAction
class XprovinceMgrAction extends AuditBaseAction
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

    public function doGetxprovinces () {
        $list = JsonAddress::getArrayXprovince();

        $this->result['data'] = $list;
        return self::TEXTJSON;
    }
}
