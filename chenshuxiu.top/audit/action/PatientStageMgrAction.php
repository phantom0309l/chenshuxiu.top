<?php

// PatientStageMgrAction
class PatientStageMgrAction extends AuditBaseAction
{

    public function doList () {
        $patientstage_title = XRequest::getValue('patientstage_title', '');

        $cond = "";
        $bind = [];

        if ($patientstage_title) {
            $cond .= " and title like :title ";
            $bind[':title'] = "%{$patientstage_title}%";
        }

        $cond .= " order by pos asc, id desc ";

        $patientstages = Dao::getEntityListByCond('PatientStage', $cond, $bind);

        XContext::setValue('patientstage_title', $patientstage_title);
        XContext::setValue('patientstages', $patientstages);

        return self::SUCCESS;
    }

    private function checkTitle ($title) {
        DBC::requireNotEmpty($title, "title不能为空");

        $patientstage = PatientStageDao::getByTitle($title);

        if ($patientstage instanceof patientstage) {
            return 'fail';
        } else {
            return 'ok';
        }
    }

    public function doAddOrModifyJson () {
        $patientstageid = XRequest::getValue('patientstageid', 0);
        $pos = XRequest::getValue('pos', 0);
        $title = XRequest::getValue('title', 0);
        $content = XRequest::getValue('content', 0);

        if ($patientstageid) {
            // 修改
            $patientstage = PatientStage::getById($patientstageid);
            DBC::requireNotEmpty($patientstage, "patientstage is null");

            if ($patientstage->title != $title) {
                // 和其他存在的组名相同
                $check_result_str = $this->checkTitle($title);
                if ($check_result_str != 'ok') {
                    echo $check_result_str;
                    return self::BLANK;
                }
            }

            $patientstage->pos = $pos;
            $patientstage->content = $content;

            if ($patientstage->title != '首次组') {
                $patientstage->title = $title;
            }
        } else {
            // 添加
            $check_result_str = $this->checkTitle($title);
            // 已存在同名的组
            if ($check_result_str != 'ok') {
                echo $check_result_str;
                return self::BLANK;
            }

            $sql = "select max(id) from patientstages";
            $max_patientstageid = Dao::queryValue($sql);
            $max_patientstageid = $max_patientstageid ?? 0;

            $row = [];

            $row['id'] = $max_patientstageid + 1;
            $row['pos'] = $pos;
            $row['title'] = $title;
            $row['content'] = $content;
            $row['create_auditorid'] = $this->myauditor->id;

            $patientstage = PatientStage::createByBiz($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doDeleteJson () {
        $patientstageid = XRequest::getValue('patientstageid', 0);
        $patientstage = PatientStage::getById($patientstageid);
        DBC::requireNotEmpty($patientstage, "patientstage is null");

        if ($patientstage->getPatientCnt() > 0) {
            echo 'fail-notempty';
        } else {
            echo 'ok';
            $patientstage->remove();
        }

        return self::BLANK;
    }
}
