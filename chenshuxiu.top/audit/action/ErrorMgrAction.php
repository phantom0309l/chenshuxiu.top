<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:03
 */
class ErrorMgrAction extends AuditBaseAction
{

    public function doError () {
        $errmsg = XRequest::getValue('errmsg', '');
        XContext::setValue("errmsg", $errmsg);
        return self::SUCCESS;
    }

    public function doAddResource () {
        $action_add = XRequest::getValue('action_add', '');
        $method_add = XRequest::getValue('method_add', '');

        $action_add = strtolower($action_add);
        $method_add = strtolower($method_add);

        $row = array();
        $row["title"] = "/{$action_add}/{$method_add}";
        $row["action"] = $action_add;
        $row["method"] = $method_add;

        $auditresource = AuditResource::createByBiz($row);
        $auditor = $this->myauditor;

        $auditrole = AuditRoleDao::getByCode('tech');

        if (in_array($auditrole->id, $auditor->getAuditRoleIdArr())) {
            XContext::setJumpPath("/auditresourcemgr/modify?auditresourceid={$auditresource->id}");
        } else {
            $error = "你当前没有权限访问{$action_add}  {$method_add}<br/>请联系技术配置权限，：）";
            XContext::setJumpPath("/errormgr/error?errmsg=" . urlencode("$error"));
        }

        return self::SUCCESS;
    }
}
