<?php

class DiseaseGroupMgrAction extends AuditBaseAction
{

    public function doList () {
        $diseasegroups = Dao::getEntityListByCond('DiseaseGroup');

        XContext::setValue("diseasegroups", $diseasegroups);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $name = XRequest::getValue('name');

        DBC::requireNotNull($name, '组名不能为空');

        $row = [];
        $row['name'] = $name;
        $diseasegroup = DiseaseGroup::createByBiz($row);

        XContext::setJumpPath("/diseasegroupmgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $diseasegroupid = XRequest::getValue('diseasegroupid');

        $diseasegroup = DiseaseGroup::getById($diseasegroupid);

        DBC::requireNotNull($diseasegroup, '疾病分组不存在');

        XContext::setValue('diseasegroup', $diseasegroup);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $diseasegroupid = XRequest::getValue('diseasegroupid');
        $name = XRequest::getValue('name');

        DBC::requireNotNull($diseasegroupid, 'diseasegroupid不能为空');
        DBC::requireNotNull($name, '分组名不能为空');

        $diseasegroup = DiseaseGroup::getById($diseasegroupid);
        DBC::requireNotNull($diseasegroup, '疾病分组不存在');

        $diseasegroup->name = $name;

        XContext::setJumpPath("/diseasegroupmgr/list");
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $diseasegroupid = XRequest::getValue("diseasegroupid");
        DBC::requireNotNull($diseasegroupid, 'diseasegroupid不能为空');

        $diseasegroup = DiseaseGroup::getById($diseasegroupid);
        DBC::requireNotNull($diseasegroup, '疾病分组不存在');

        // $diseasegroup->remove();

        return self::TEXTJSON;
    }
}
