<?php
// Dc_projectMgrAction
class Dc_projectMgrAction extends AuditBaseAction
{

    public function doList () {
        $dc_projects = Dao::getEntityListByCond('Dc_project', ' order by id desc ');

        XContext::setValue('dc_projects', $dc_projects);

        return self::SUCCESS;
    }

    public function doAddorModifyJson () {
        $dc_projectid = XRequest::getValue('dc_projectid', 0);
        $title = XRequest::getValue('title', '');
        $reportor = XRequest::getValue('reportor', '');
        $report_email = XRequest::getValue('report_email', '');
        $content = XRequest::getValue('content', '');

        $dc_project = Dc_project::getById($dc_projectid);
        // ----------------------------------------------检验数据----------------------------------------------
        // 校验项目名称
        if ($title == '') {
            echo '项目名称不能为空';

            return self::BLANK;
        } else {
            if (($dc_project instanceof Dc_project && $dc_project->title != $title) || (false == $dc_project instanceof Dc_project)) {
                $dc_project = Dc_projectDao::getByTitle($title);

                if ($dc_project instanceof Dc_project) {
                    echo '项目名称已存在，请重新输入';

                    return self::BLANK;
                }
            }
        }

        // 检验汇报人
        if ($reportor == '') {
            echo '汇报人不能为空';

            return self::BLANK;
        }

        // 校验汇报人邮箱
        if ($report_email == '') {
            echo '汇报人邮箱不能为空';

            return self::BLANK;
        } else {
            $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if (! preg_match( $pattern, $report_email)){
                echo '电子邮件地址不合法，请重新输入';

                return self::BLANK;
            }
        }

        if ($dc_project instanceof Dc_project) {
            $dc_project->title = $title;
            $dc_project->reportor = $reportor;
            $dc_project->report_email = $report_email;
            $dc_project->content = $content;

            echo "success-modify";
        } else {
            $row = [];
            $row['title'] = $title;
            $row['reportor'] = $reportor;
            $row['report_email'] = $report_email;
            $row['content'] = $content;
            $row['create_auditorid'] = $this->myauditor->id;;
            $dc_project = Dc_project::createByBiz($row);

            echo "success-add";
        }

        return self::BLANK;
    }

    public function doDeleteJson () {
        $dc_projectid = XRequest::getValue('dc_projectid', 0);
        $dc_project = Dc_project::getById($dc_projectid);
        DBC::requireNotEmpty($dc_project, "dc_project is null");

        if ($dc_project->getCntDoctorProject() <= 0) {
            $dc_project->remove();

            echo "success";
        } else {
            echo "fail";
        }

        return self::BLANK;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
