<?php
// Dc_doctorProjectMgrAction
class Dc_doctorProjectMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $dc_projectid = XRequest::getValue('dc_projectid', 0);

        $dc_projects = Dao::getEntityListByCond('Dc_project');

        $cond = "";
        $bind = [];

        if ($doctorid) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($dc_projectid) {
            $cond .= " and dc_projectid = :dc_projectid ";
            $bind[':dc_projectid'] = $dc_projectid;
        }

        $dc_doctorprojects = Dao::getEntityListByCond('Dc_doctorProject', $cond, $bind);

        XContext::setValue('dc_projectid', $dc_projectid);
        XContext::setValue('dc_projects', $dc_projects);
        XContext::setValue('dc_doctorprojects', $dc_doctorprojects);

        return self::SUCCESS;
    }

    public function doAddJson () {
        $title = XRequest::getValue('title', '');
        $dc_projectid = XRequest::getValue('dc_projectid', 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $begin_date = XRequest::getValue('begin_date', '');
        $end_date = XRequest::getValue('end_date', '');
        $frequency = XRequest::getValue('frequency', 0);
        $period = XRequest::getValue('period', 0);
        $papertplids = XRequest::getValue('papertplids', '');
        $is_auto_open_next = XRequest::getValue('is_auto_open_next', -1);
        $content = XRequest::getValue('content', '');
        $send_content_tpl = XRequest::getValue('send_content_tpl', '');
        $bulletin = XRequest::getValue('bulletin', '');

        $row = [];
        $row['title'] = $title;
        $row['dc_projectid'] = $dc_projectid;
        $row['doctorid'] = $doctorid;
        $row['begin_date'] = $begin_date;
        $row['end_date'] = $end_date;
        $row['frequency'] = $frequency;
        $row['period'] = $period;
        $row['papertplids'] = $papertplids;
        $row['is_auto_open_next'] = $is_auto_open_next;
        $row['content'] = $content;
        $row['send_content_tpl'] = $send_content_tpl;
        $row['bulletin'] = $bulletin;
        $row['create_auditorid'] = $this->myauditor->id;
        $dc_doctorproject = Dc_doctorProject::createByBiz($row);

        echo 'success';

        return self::BLANK;
    }

    public function doModifyJson () {
        $dc_doctorprojectid = XRequest::getValue('dc_doctorprojectid', 0);
        $begin_date = XRequest::getValue('begin_date', '');
        $end_date = XRequest::getValue('end_date', '');
        $frequency = XRequest::getValue('frequency', 0);
        $period = XRequest::getValue('period', 0);
        $papertplids = XRequest::getValue('papertplids', '');
        $is_auto_open_next = XRequest::getValue('is_auto_open_next', -1);
        $content = XRequest::getValue('content', '');
        $send_content_tpl = XRequest::getValue('send_content_tpl', '');
        $bulletin = XRequest::getValue('bulletin', '');

        $dc_doctorproject = Dc_doctorProject::getById($dc_doctorprojectid);
        if (false == $dc_doctorproject instanceof Dc_doctorProject) {
            echo 'fail';

            return self::BLANK;
        }

        $dc_doctorproject->begin_date = $begin_date;
        $dc_doctorproject->end_date = $end_date;
        $dc_doctorproject->frequency = $frequency;
        $dc_doctorproject->period = $period;
        $dc_doctorproject->papertplids = $papertplids;
        $dc_doctorproject->is_auto_open_next = $is_auto_open_next;
        $dc_doctorproject->content = $content;
        $dc_doctorproject->send_content_tpl = $send_content_tpl;
        $dc_doctorproject->bulletin = $bulletin;

        echo 'success';

        return self::BLANK;
    }

    public function doDeleteJson () {
        $dc_doctorprojectid = XRequest::getValue('dc_doctorprojectid', 0);
        $dc_doctorproject = Dc_doctorProject::getById($dc_doctorprojectid);

        if ($dc_doctorproject instanceof Dc_doctorProject) {
            $cnt = $dc_doctorproject->getDc_patientplanCnt();
            if ($cnt <= 0) {
                $dc_doctorproject->remove();
                echo "success";
            } else {
                echo "fail-cnt";
            }
        } else {
            echo "fail-not";
        }

        return self::BLANK;
    }
}
