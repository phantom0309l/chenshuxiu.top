<?php

class WxVoiceMsgMgrAction extends AuditBaseAction
{

    // 修改conotent接口
    public function doModifyContentJson () {
        $wxvoicemsgid = XRequest::getValue("wxvoicemsgid", 0);
        DBC::requireNotEmpty($wxvoicemsgid, 'wxvoicemsgid is null');
        $content = XRequest::getValue("content", '');

        $wxvoicemsg = WxVoiceMsg::getById($wxvoicemsgid);
        DBC::requireTrue($wxvoicemsg instanceof WxVoiceMsg, "wxvoicemsg不存在{$wxvoicemsgid}");

        $wxvoicemsg->content = $content;

        echo "ok";
        return self::BLANK;
    }
}
