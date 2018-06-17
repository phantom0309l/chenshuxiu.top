<?php

class AuditorGroupMgrAction extends AuditBaseAction
{

    // 员工组列表
    public function doList() {
        $type = XRequest::getValue("type", 'all');

        $cond = '';
        $bind = [];

        if ('all' != $type) {
            $cond .= ' AND type = :type ';
            $bind[':type'] = $type;
        }

        $auditorGroups = Dao::getEntityListByCond("AuditorGroup", $cond, $bind);

        XContext::setValue("type", $type);
        XContext::setValue("auditorGroups", $auditorGroups);

        return self::SUCCESS;
    }

    // 新建
    public function doAdd() {
        $arr = AuditorGroupDao::getEnamesByType('base');
        $enames = [];
        $enames[0] = '请选择...';

        foreach ($arr as $ename) {
            $enames[$ename] = $ename;
        }

        XContext::setValue('enames', $enames);
        return self::SUCCESS;
    }

    // 员工组新建提交
    public function doAddPost() {
        $type = XRequest::getValue("type", '');
        $ename = XRequest::getValue("ename", '');
        $name = XRequest::getValue("name", '');
        DBC::requireNotEmpty($type, '类型不能为空');
        DBC::requireNotEmpty($ename, 'ename不能为空');
        DBC::requireNotEmpty($name, '名字不能为空');

        $auditorIdArr = XRequest::getValue("auditorids", array());

        $auditorGroup = AuditorGroupDao::getByName($name);
        if ($auditorGroup instanceof AuditorGroup) {
            XContext::setJumpPath("/auditorgroupmgr/add?preMsg=此名字已被占用");
            return self::SUCCESS;
        }

        $auditorGroup = AuditorGroupDao::getByTypeAndEname($type, $ename);
        if ($auditorGroup instanceof AuditorGroup) {
            XContext::setJumpPath("/auditorgroupmgr/add?preMsg=此类型下的ename已被占用");
            return self::SUCCESS;
        }

        $row = array();
        $row["type"] = $type;
        $row["ename"] = $ename;
        $row["name"] = $name;

        $auditorGroup = AuditorGroup::createByBiz($row);

        if (false == empty($auditorIdArr)) {
            foreach ($auditorIdArr as $auditorId) {
                $row = [
                    'auditorid' => $auditorId,
                    'auditorgroupid' => $auditorGroup->id
                ];

                AuditorGroupRef::createByBiz($row);
            }
        }

        XContext::setJumpPath("/auditorgroupmgr/list");
        return self::SUCCESS;
    }

    // 员工组修改
    public function doModify() {
        $auditorGroupId = XRequest::getValue('auditorgroupid', 0);
        $auditorGroup = AuditorGroup::getById($auditorGroupId);

        $arr = AuditorGroupDao::getEnamesByType('base');
        $enames = [];
        $enames[0] = '请选择...';

        foreach ($arr as $ename) {
            $enames[$ename] = $ename;
        }

        XContext::setValue('enames', $enames);
        XContext::setValue('auditorGroup', $auditorGroup);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $auditorGroupId = XRequest::getValue('auditorgroupid', 0);
        $auditorGroup = AuditorGroup::getById($auditorGroupId);
        $type = XRequest::getValue('type', $auditorGroup->type);
        $ename = XRequest::getValue('ename', $auditorGroup->ename);
        $name = XRequest::getValue('name', $auditorGroup->name);

        $auditorIdArr = XRequest::getValue("auditorids", array());

        if ($name != $auditorGroup->name) {
            $auditorGroupElse = AuditorGroupDao::getByName($name);
            if ($auditorGroupElse instanceof AuditorGroup) {
                XContext::setJumpPath("/auditorgroupmgr/modify?auditorgroupid={$auditorGroupId}&preMsg=此名字已被占用");
                return self::SUCCESS;
            }

            $auditorGroup->name = $name;
        }

        if ($ename != $auditorGroup->ename) {
            $auditorGroupElse = AuditorGroupDao::getByTypeAndEname($type, $ename);
            if ($auditorGroupElse instanceof AuditorGroup) {
                XContext::setJumpPath("/auditorgroupmgr/modify?auditorgroupid={$auditorGroupId}&preMsg=此类型下的ename已被占用");
                return self::SUCCESS;
            }

            $auditorGroup->ename = $ename;
        }

        $auditorGroup->type = $type;

        AuditorGroupRefService::updateAllOfAuditorGroup($auditorGroup, $auditorIdArr);

        XContext::setJumpPath('/auditorgroupmgr/list');
        return self::SUCCESS;
    }

    public function doAjaxDeletePost() {
        $auditorGroupId = XRequest::getValue('auditorgroupid', 0);
        $auditorGroup = AuditorGroup::getById($auditorGroupId);
        DBC::requireTrue($auditorGroup instanceof AuditorGroup, '分组不存在！');

        $auditors = $auditorGroup->getAuditors();
        DBC::requireEmpty($auditors, '请先将员工移除该分组！');

        $auditorGroup->remove();

        return self::TEXTJSON;
    }
}
