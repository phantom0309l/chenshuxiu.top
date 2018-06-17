<?php

class SmsMgrAction extends AuditBaseAction
{

    // 列表
    public function doList () {
        $smss = Dao::getEntityListByCond("Sms");

        XContext::setValue("smss", $smss);
        return self::SUCCESS;
    }

    // admin.com/smsmgr/sendbox?sub_sms
    // 短信工具
    // get 'admin/sub_sms', to: 'admin/tools#index’
    // --------
    // admin.com/smsmgr/sendbox?tools
    // 短信工具（重复）
    // get 'admin/tools', to: 'admin/tools#index’
    public function doSendBox () {
        return self::SUCCESS;
    }

    // admin.com/smsmgr/send/
    // 批量发短信
    // post 'api/sms/send', to: 'admin/tools#sendsms’
    public function doSend () {
        return self::SUCCESS;
    }

}
