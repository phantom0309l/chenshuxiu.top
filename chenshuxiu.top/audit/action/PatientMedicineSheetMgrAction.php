<?php

// PatientMedicineSheetMgrAction
class PatientMedicineSheetMgrAction extends AuditBaseAction
{

    // 患者用药表单列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');


        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $cond = "";
        $bind = [];

        $diseaseidstr = $this->getContextDiseaseidStr();
        $cond .= " and x.diseaseid in ($diseaseidstr) ";

        if ($patientid) {
            $cond .= " and p.id = :patientid ";
            $bind[':patientid'] = $patientid;
        }

        if ($doctorid) {
            $cond .= " and x.doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        if ($patient_name) {
            $cond .= " and p.name like :name ";
            $bind[':name'] = "%{$patient_name}%";
        }

        $cond .= " and p.auditstatus = 1 ";
        $cond .= " order by pms.createtime desc ";

        $sql = "select distinct p.*
                from patients p
                inner join pcards x on x.patientid = p.id
                left join
                (
                    select *
                    from patientmedicinesheets
                    GROUP BY patientid
                    ORDER BY thedate desc
                ) pms ON pms.patientid = p.id
                where 1 = 1 ";

        $sql .= $cond;
        $patients = Dao::loadEntityList4Page("Patient", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(distinct p.id)
                     from patients p
                     inner join pcards x on x.patientid = p.id
                     left join
                     (
                         select *
                         from patientmedicinesheets
                         GROUP BY patientid
                         ORDER BY thedate desc
                     ) pms ON pms.patientid = p.id
                     where 1 = 1 ";

        $countSql .= $cond;

        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/patientmedicinesheetmgr/list?doctorid={$doctorid}&patient_name={$patient_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);
        // 翻页end

        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    public function doOneHtml () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotNull($patient, "患者不能为空");

        XContext::setValue('patient', $patient);

        $pmsheets = PatientMedicineSheetDao::getListByPatient($patient);
        XContext::setValue('pmsheets', $pmsheets);

        $pmtargets = PatientMedicineTargetDao::getListByPatient($patient);
        XContext::setValue('pmtargets', $pmtargets);

        $pmpkgs = PatientMedicinePkgDao::getListByPatientid($patient->id);
        XContext::setValue('pmpkgs', $pmpkgs);

        // 20170419 TODO by sjp : 为啥要获取 openid ?
        $wxuser = $patient->getMasterWxUser();
        XContext::setValue('openid', $wxuser->openid);

        return self::SUCCESS;
    }

    // 患者用药规范发送
    public function doSendMsgJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $mydisease = $this->mydisease;
        $myauditor = $this->myauditor;

        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, 'patient不能为空');

        $pcards = PcardDao::getListByPatient($patient);

        if (count($pcards) > 1) {
            DBC::requireNotEmpty($this->mydisease, "必须选疾病");
            $pcard = $patient->getOnePcardByDiseaseid($mydisease->id);
        } elseif (count($pcards) == 1) {
            $pcard = $patient->getMasterPcard();
        } else {
            DBC::requireTrue(false, "[{$patient->id}] 患者没有pcard");
        }

        // ---------------------------向患者推送消息end---------------------------
        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . "/patientmedicinesheet/one";

        $first = array(
            "value" => "患者用药核对",
            "color" => "");
        $keyword2 = "您好，请填写用药核对表，我们会对您的用药记录进行核对。如有问题及时纠正，以减少因错误服药导致的疗效下降和不良反应增多的问题。";

        $keywords = array(
            array(
                "value" => "{$pcard->doctor->name}",
                "color" => "#ff6600"),
            array(
                "value" => $keyword2,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        PushMsgService::sendTplMsgToWxUsersOfPcardByAuditor($pcard, $myauditor, "doctornotice", $content, $url);
        // ---------------------------向患者推送消息end---------------------------

        $pcard->send_pmsheet_status = 1;

        echo 'success';
        return self::BLANK;
    }

    public function doSaveAllJson() {
        $data = XRequest::getValue('data', []);
        DBC::requireTrue(is_array($data), '数据格式不是数组');
        $patientmedicinesheetid = XRequest::getValue('patientmedicinesheetid', 0);
        $pmsheet = PatientMedicineSheet::getById($patientmedicinesheetid);
        DBC::requireNotEmpty($pmsheet, 'pmsheet 不存在');

        $now = date('Y-m-d H:i:s');
        foreach ($data as $one) {
            $pmsitemid = $one['patientmedicinesheetitemid'];
            $pmsitem = PatientMedicineSheetItem::getById($pmsitemid);
            $pmsitem->auditlog .= "{$now} {$this->myauditor->name} 操作 {$pmsitem->drug_dose}=>{$one['drug_dose']} {$pmsitem->drug_frequency}=>{$one['drug_frequency']} {$pmsitem->status}=>{$one['status']} {$pmsitem->auditremark}=>{$one['auditremark']}";
            $pmsitem->drug_dose = $one['drug_dose'];
            $pmsitem->drug_frequency = $one['drug_frequency'];
            $pmsitem->auditremark = $one['auditremark'];
            $pmsitem->status = $one['status'];
        }

        echo 'ok';
        return self::BLANK;
    }

    public function doAuditJson () {
        echo 'fail';
        return self::BLANK;

        $patientmedicinesheetid = XRequest::getValue('patientmedicinesheetid', 0);
        $pmsheet = PatientMedicineSheet::getById($patientmedicinesheetid);

        // $pmsheet->audit($this->myauditor->id); // 此函数已改

        echo 'success';
        return self::BLANK;
    }

    public function doAuditRightJson() {
        $patientmedicinesheetid = XRequest::getValue('patientmedicinesheetid', 0);
        $pmsheet = PatientMedicineSheet::getById($patientmedicinesheetid);

        $myauditor = $this->myauditor;

        $pmsheet->auditRight($myauditor->id); // 此函数已改

        $content = '您的用药反馈已收到，经核实没有发现错误，请继续按照目前的方法服药。我们每个月都将与您核对一次用药，如遇问题请及时跟我们联系';
        $wxuser = $pmsheet->wxuser;
        // 如果指定了wxuser则只发该wxuser，否则全部发送
        if ($wxuser instanceof WxUser) {
            PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
        } else {
            $patient = $pmsheet->patient;
            if ($patient instanceof Patient) {
                $wxusers = $patient->getWxUsers();
                foreach ($wxusers as $wxuser) {
                    PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);
                }
            }
        }

        echo 'success';
        return self::BLANK;
    }

    public function doAuditWrongJson () {
        $patientmedicinesheetid = XRequest::getValue('patientmedicinesheetid', 0);
        $pmsheet = PatientMedicineSheet::getById($patientmedicinesheetid);

        $pmsheet->auditWrong($this->myauditor->id); // 此函数已改

        echo 'success';
        return self::BLANK;
    }
}
