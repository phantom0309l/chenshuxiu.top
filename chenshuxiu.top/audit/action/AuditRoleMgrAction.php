<?php
// AuditRoleMgrAction
class AuditRoleMgrAction extends AuditBaseAction
{

    // 运营角色列表
    public function doList () {
        $auditRoles = Dao::getEntityListByCond('AuditRole');
        XContext::setValue("auditRoles", $auditRoles);
        return self::SUCCESS;
    }

    // 运营角色新建
    public function doAdd () {
        return self::SUCCESS;
    }

    // 运营角色新建提交
    public function doAddPost () {
        $code = XRequest::getValue("code", '');
        $name = XRequest::getValue("name", '');

        DBC::requireNotEmpty($code, 'code不能为空');
        DBC::requireNotEmpty($name, 'name不能为空');

        $sql = "select max(id) as maxid from auditroles";
        $maxid = 0 + Dao::queryValue($sql, []);

        $row = array();
        $row["id"] = $maxid + 1;
        $row["code"] = $code;
        $row["name"] = $name;

        $auditRole = AuditRole::createByBiz($row);

        XContext::setJumpPath("/auditrolemgr/list");
        return self::SUCCESS;
    }
}
