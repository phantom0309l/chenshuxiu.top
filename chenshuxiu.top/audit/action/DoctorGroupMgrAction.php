<?php
// DoctorGroupMgrAction
class DoctorGroupMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorgroup_title = XRequest::getValue('doctorgroup_title', '');

        $cond = "";
        $bind = [];

        if ($doctorgroup_title) {
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$doctorgroup_title}%";
        }

        $cond .= " order by id desc ";

        $doctorgroups = Dao::getEntityListByCond('DoctorGroup', $cond, $bind);

        XContext::setValue('doctorgroup_title', $doctorgroup_title);
        XContext::setValue('doctorgroups', $doctorgroups);

        return self::SUCCESS;
    }

    private function checkTitle ($title) {
        DBC::requireNotEmpty($title, "title不能为空");

        $doctorgroup = DoctorGroupDao::getByTitle($title);

        if ($doctorgroup instanceof doctorgroup) {
            return 'fail';
        } else {
            return 'ok';
        }
    }

    public function doAddOrModifyJson () {
        $doctorgroupid = XRequest::getValue('doctorgroupid', 0);
        $title = XRequest::getValue('title', 0);
        $content = XRequest::getValue('content', 0);

        if ($doctorgroupid) {
            // 修改
            $doctorgroup = DoctorGroup::getById($doctorgroupid);
            DBC::requireNotEmpty($doctorgroup, "doctorgroup is null");

            if ($doctorgroup->title != $title) {
                $check_result_str = $this->checkTitle($title);
                if ($check_result_str != 'ok') {
                    echo $check_result_str;

                    return self::BLANK;
                }
            }

            $doctorgroup->title = $title;
            $doctorgroup->content = $content;
        } else {
            // 添加
            $check_result_str = $this->checkTitle($title);
            if ($check_result_str != 'ok') {
                echo $check_result_str;

                return self::BLANK;
            }

            $row = [];

            $row['title'] = $title;
            $row['content'] = $content;
            $row['create_auditorid'] = $this->myauditor->id;

            $doctorgroup = DoctorGroup::createByBiz($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doDeleteJson () {
        $doctorgroupid = XRequest::getValue('doctorgroupid', 0);
        $doctorgroup = DoctorGroup::getById($doctorgroupid);
        DBC::requireNotEmpty($doctorgroup, "doctorgroup is null");

        if ($doctorgroup->getDoctorCnt() > 0) {
            echo 'fail-notempty';
        } else {
            echo 'ok';
            $doctorgroup->remove();
        }

        return self::BLANK;
    }
}
