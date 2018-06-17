<?php
// Dc_patientPlanMgrAction
class Dc_patientPlanMgrAction extends AuditBaseAction
{

    public function doList () {
        $dc_doctorprojectid = XRequest::getValue('dc_doctorprojectid', 0);

        $patientid = XRequest::getValue('patientid', 0);

        $dc_doctorprojects = Dao::getEntityListByCond('Dc_doctorProject');

        $cond = "";
        $bind = [];

        if ($dc_doctorprojectid) {
            $cond .= " and dc_doctorprojectid = :dc_doctorprojectid ";
            $bind[':dc_doctorprojectid'] = $dc_doctorprojectid;
        }

        if ($patientid) {
            $patient = Patient::getById($patientid);
            XContext::setValue('patient', $patient);

            $cond .= " and patientid = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        $dc_patientplans = Dao::getEntityListByCond('Dc_patientPlan', $cond, $bind);

        XContext::setValue('dc_doctorprojectid', $dc_doctorprojectid);
        XContext::setValue('dc_patientplans', $dc_patientplans);
        XContext::setValue('dc_doctorprojects', $dc_doctorprojects);

        return self::SUCCESS;
    }

    // 给患者添加项目，并发送模板消息
    public function doAddJson () {
        $dc_doctorprojectid = XRequest::getValue('dc_doctorprojectid', 0);
        $dc_doctorproject = Dc_doctorProject::getById($dc_doctorprojectid);
        DBC::requireNotEmpty($dc_doctorproject, 'dc_doctorproject is null');

        $begin_date = XRequest::getValue('begin_date', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $end_date = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $dc_doctorproject->period);

        if ($begin_date < $dc_doctorproject->begin_date || $begin_date > $dc_doctorproject->end_date) {
            echo "日期超出范围";

            return self::BLANK;
        }

        if ($end_date < $dc_doctorproject->begin_date || $end_date > $dc_doctorproject->end_date) {
            echo "日期超出范围";

            return self::BLANK;
        }

        $dc_patientplan = Dc_patientPlanDao::getByPatientDc_doctorprojectBegin_date($patient, $dc_doctorproject, $begin_date);
        if ($dc_patientplan instanceof Dc_patientPlan) {
            echo "该项目与存在的项目有日期冲突";

            return self::BLANK;
        }

        // create dc_patientplan
        $row = [];
        $row["title"] = $dc_doctorproject->dc_project->title;
        $row['dc_doctorprojectid'] = $dc_doctorprojectid;
        $row["patientid"] =  $patient->id;
        $row["doctorid"] =  $patient->doctorid;
        $row["begin_date"] = $begin_date;
        $row["end_date"] = $end_date;
        $row["papertplids"] = $dc_doctorproject->papertplids;
        $row["dc_patientplan_status"] =  0;
        $row["create_auditorid"] = $this->myauditor->id;
        $dc_patientplan = Dc_patientPlan::createByBiz($row);

        // create dc_patientplanitem
        for ($daycnt = 0; $daycnt <= $dc_doctorproject->period; $daycnt += $dc_doctorproject->frequency) {
            $row = [];
            $row["dc_patientplanid"] =  $dc_patientplan->id;
            $row["patientid"] =  $patientid;
            $row["doctorid"] =  $patient->doctorid;
            $row["plan_date"] = date('Y-m-d', strtotime($begin_date) + 3600 * 24 * $daycnt);
            $row["submit_time"] = '';
            $dc_patientplanitem = Dc_patientPlanItem::createByBiz($row);
        }

        // 发送模板消息
        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . '/dc_patientplanitem/list?dc_patientplanid=' . $dc_patientplan->id;

        $firstContent = $dc_doctorproject->send_content_tpl ? $dc_doctorproject->send_content_tpl : "请尽快填写{$patient->doctor->name}医生的随访量表";
        $first = [
            "value" => $firstContent,
            "color" => ""
        ];

        $keywords = [
            [
                "value" => "{$patient->name}",
                "color" => "#ff6600"
            ],
            [
                "value" => date('Y-m-d'),
                "color" => "#ff6600"
            ],
            [
                "value" => "请点击详情进行填写",
                "color" => "#ff6600"
            ]
        ];
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToPatientByAuditor($patient, $this->myauditor, 'followupNotice', $content, $url);

        echo 'success';

        return self::BLANK;
    }
}
