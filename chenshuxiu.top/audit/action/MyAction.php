<?php

class MyAction extends AuditBaseAction
{

    // 用户个人信息
    public function doInfo () {
        // 有效代码
        $myuser = $this->myuser;
        $auditor = $myuser->getAuditor();
        $qr_ticket = $auditor->getQrTicket();
        XContext::setValue("qr_ticket", $qr_ticket);
        XContext::setValue("auditor", $auditor);

        return self::SUCCESS;
    }

    // 员工修改提交头像
    public function doModifyPicturePost () {
        $pictureid = XRequest::getValue('pictureid', 0);
        $auditorid = XRequest::getValue('auditorid', 0);

        $auditor = Auditor::getById($auditorid);

        $auditor->pictureid = $pictureid;

        XContext::setJumpPath('/my/info');
        return self::SUCCESS;
    }

    public function doModifyPassword () {
        return self::SUCCESS;
    }

    public function doModifyPasswordPost () {
        $myuser = $this->myuser;

        $password = XRequest::getValue('password', '');
        $newpassword = XRequest::getValue('newpassword', '');
        $newpasswordrepeat = XRequest::getValue('newpasswordrepeat', '');

        if ($newpassword != $newpasswordrepeat) {
            $preMsg = "重复密码不一致";
        } elseif ($myuser->validatePassword($password)) {
            $preMsg = "密码修改成功";
            $myuser->modifyPassword($newpassword);
        } else {
            $preMsg = "旧密码不正确";
        }

        XContext::setJumpPath("/my/modifypassword?preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }
}
