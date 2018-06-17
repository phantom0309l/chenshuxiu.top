<?php

class WxTxtMsgMgrAction extends AuditBaseAction
{

    // 列表
    public function doList () {
        $wxtxtmsgs = Dao::getEntityListByCond("WxTxtMsg", ' order by id desc limit 10 ');

        XContext::setValue("wxtxtmsgs", $wxtxtmsgs);
        return self::SUCCESS;
    }
}
