<?php

// patientgroupMgrAction
class PatientGroupMgrAction extends AuditBaseAction
{

    public function doList () {
        $patientgroup_title = XRequest::getValue('patientgroup_title', '');

        $cond = "";
        $bind = [];

        if ($patientgroup_title) {
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$patientgroup_title}%";
        }

        $cond .= " order by pos asc, id desc ";

        $patientgroups = Dao::getEntityListByCond('PatientGroup', $cond, $bind);

        XContext::setValue('patientgroup_title', $patientgroup_title);
        XContext::setValue('patientgroups', $patientgroups);

        return self::SUCCESS;
    }

    private function checkTitle ($title) {
        DBC::requireNotEmpty($title, "title不能为空");

        $patientgroup = PatientGroupDao::getByTitle($title);

        if ($patientgroup instanceof PatientGroup) {
            return 'fail';
        } else {
            return 'ok';
        }
    }

    public function doAddOrModifyJson () {
        $patientgroupid = XRequest::getValue('patientgroupid', 0);
        $pos = XRequest::getValue('pos', 0);
        $title = XRequest::getValue('title', 0);
        $content = XRequest::getValue('content', 0);

        if ($patientgroupid) {
            // 修改
            $patientgroup = PatientGroup::getById($patientgroupid);
            DBC::requireNotEmpty($patientgroup, "patientgroup is null");

            if ($patientgroup->title != $title) {
                // 和其他存在的组名相同
                $check_result_str = $this->checkTitle($title);
                if ($check_result_str != 'ok') {
                    echo $check_result_str;
                    return self::BLANK;
                }
            }

            $patientgroup->pos = $pos;
            $patientgroup->content = $content;

            if ($patientgroup->title != '首次组') {
                $patientgroup->title = $title;
            }
        } else {
            // 添加
            $check_result_str = $this->checkTitle($title);
            // 已存在同名的组
            if ($check_result_str != 'ok') {
                echo $check_result_str;
                return self::BLANK;
            }

            $sql = "select max(id) from patientgroups";
            $max_patientgroupid = Dao::queryValue($sql);
            $max_patientgroupid = $max_patientgroupid ?? 0;

            $row = [];

            $row['id'] = $max_patientgroupid + 1;
            $row['pos'] = $pos;
            $row['title'] = $title;
            $row['content'] = $content;
            $row['create_auditorid'] = $this->myauditor->id;

            $patientgroup = PatientGroup::createByBiz($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doDeleteJson () {
        $patientgroupid = XRequest::getValue('patientgroupid', 0);
        $patientgroup = PatientGroup::getById($patientgroupid);
        DBC::requireNotEmpty($patientgroup, "patientgroup is null");

        if ($patientgroup->getPatientCnt() > 0) {
            echo 'fail-notempty';
        } else {
            echo 'ok';
            $patientgroup->remove();
        }

        return self::BLANK;
    }
}
