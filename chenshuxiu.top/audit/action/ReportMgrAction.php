<?php

class ReportMgrAction extends AuditBaseAction
{

    public function doAdd () {
        $patientid = XRequest::getValue('patientid', 0);
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, 'patient 不存在');

        $doctorid = XRequest::getValue('doctorid', 0);
        if ($doctorid == 0) {
            $doctorid = $patient->doctorid;
        }
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, 'doctor 不存在');

        $reporttplid = XRequest::getValue('reporttplid', 0);
        if (empty($reporttplid)) {
            $reportTpl = ReportTplDao::getOne();
        } else {
            $reportTpl = ReportTpl::getById($reporttplid);
        }
        DBC::requireTrue($reportTpl instanceof ReportTpl, 'reportTpl 不存在');

        $pcard = $patient->getPcardByDoctorid($doctor->id);
        $diseaseid = null;
        if ($pcard instanceof Pcard) {
            $diseaseid = $pcard->diseaseid;
        }

        $config = json_decode($reportTpl->content, true);
        foreach ($config as $item) {
            switch ($item) {
                case 'baseInfo': // 基本信息
                    $revisitrecord = RevisitRecordDao::getLastByPatientidDoctorid($patientid, $doctorid);
                    XContext::setValue('revisitrecord', $revisitrecord);
                    break;
                case 'patientRemark': // 症状体征及不良反应
                    $cond = " patientid = :patientid AND doctorid = :doctorid ";

                    $sql = "SELECT pr.*
                            FROM (SELECT *
                            FROM patientremarks
                            WHERE {$cond}
                            ORDER BY thedate DESC) pr
                            GROUP BY pr.name
                            ORDER BY pr.thedate DESC;";

                    $bind = [
                        ':patientid' => $patientid,
                        ':doctorid' => $doctorid];

                    $patientRemarks = Dao::loadEntityList('PatientRemark', $sql, $bind);
                    XContext::setValue('patientRemarks', $patientRemarks);
                    break;
                case 'patientmedicinepkg': // 用药
                    $pmtargets = PatientMedicineTargetDao::getListByPatientIdAndDoctorId($patientid, $doctorid);
                    XContext::setValue('pmtargets', $pmtargets);

                    break;
                case 'checkuptpls': // 检查报告
                    $checkuptpls = CheckupTplDao::getListByDoctorAndDiseaseid_isInTkt_isInAdmin($doctor, $diseaseid, null, 1);
                    $checkup_arr = [];
                    foreach ($checkuptpls as $checkuptpl) {
                        $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $checkuptpl);
                        if (empty($checkups)) {
                            continue;
                        }
                        $tmp_checkups = [];
                        $max = 3;
                        foreach ($checkups as $i => $checkup) {
                            if ($i == $max) {
                                break;
                            }
                            if ($checkup->xanswersheetid == 0) {
                                continue;
                            }
                            $tmp_checkups[] = $checkup;
                        }
                        if (empty($tmp_checkups)) {
                            continue;
                        }
                        $checkup_arr[] = [
                            "checkuptpl" => $checkuptpl,
                            "checkups" => $tmp_checkups];
                    }
                    XContext::setValue('checkup_arr', $checkup_arr);
                    break;
                case 'diagnose': // 诊断和分期
                                 // 最新诊断
                    $diagnosePatientRecord = Dao::getEntityByCond('PatientRecord',
                            ' AND patientid=:patientid AND code="cancer" AND type="diagnose" ORDER BY thedate DESC LIMIT 1',
                            [
                                ':patientid' => $pcard->patientid]);
                    XContext::setValue('diagnosePatientRecord', $diagnosePatientRecord);
                    // 最新分期
                    $stagingPatientRecord = Dao::getEntityByCond('PatientRecord',
                            ' AND patientid=:patientid AND code="cancer" AND type="staging" ORDER BY thedate DESC LIMIT 1',
                            [
                                ':patientid' => $pcard->patientid]);
                    XContext::setValue('stagingPatientRecord', $stagingPatientRecord);
                    break;
                case 'chemo': // 最新化疗方案
                    $chemoPatientRecord = Dao::getEntityByCond('PatientRecord',
                            ' AND patientid=:patientid AND code="cancer" AND type="chemo" ORDER BY thedate DESC LIMIT 1',
                            [
                                ':patientid' => $pcard->patientid]);
                    XContext::setValue('chemoPatientRecord', $chemoPatientRecord);
                    break;
                case 'nexthualiaodate': // 下次诊疗日期
                    $chemoPatientRecord = Dao::getEntityByCond('PatientRecord',
                            ' AND patientid=:patientid AND code="cancer" AND type="chemo" ORDER BY thedate DESC LIMIT 1',
                            [
                                ':patientid' => $pcard->patientid]);
                    $nextHualiaoDate = '';
                    // 如果没有化疗或最新一次的方案日期+周数距今超过一周则不展示该模块
                    if ($chemoPatientRecord) {
                        $content = $chemoPatientRecord->loadJsonContent();
                        $week = 3;
                        if ($content['cycle'] == '两周方案') {
                            $week = 2;
                        } else
                            if ($content['cycle'] == '三周方案') {
                                $week = 3;
                            } else
                                if ($content['cycle'] == '四周方案') {
                                    $week = 4;
                                }
                        //
                        $t = strtotime($chemoPatientRecord->thedate);
                        $now = strtotime(date("Y-m-d"));
                        if ($t + $week * 7 * 86400 < $now) {} else {
                            $nextHualiaoDate = date('Y-m-d', strtotime("+{$week}week", $t));
                        }
                    }
                    XContext::setValue('nextHualiaoDate', $nextHualiaoDate);
                    break;
                case 'wbc_checkup': // 近期血常规
                    $wbcCheckupPatientRecord = Dao::getEntityByCond('PatientRecord',
                            ' AND patientid=:patientid AND code="cancer" AND type="wbc_checkup" ORDER BY id DESC LIMIT 1',
                            [
                                ':patientid' => $pcard->patientid]);
                    XContext::setValue('wbcCheckupPatientRecord', $wbcCheckupPatientRecord);
                    break;
            }
        }

        $reportTpls = ReportTplDao::getAll();
        XContext::setValue('reportTpls', $reportTpls);

        XContext::setValue('reportTpl', $reportTpl);
        XContext::setValue('patient', $patient);
        XContext::setValue('doctor', $doctor);
        XContext::setValue('pcards', $patient->getPcards());
        XContext::setValue('pcard', $pcard);

        return self::SUCCESS;
    }

    public function doSendPost () {
        $appeal = XRequest::getValue("appeal", "");
        $remark = XRequest::getValue("remark", "");
        $pictureids = XRequest::getValue("pictureids", []);

        $doctorid = XRequest::getValue("doctorid", 0);
        $patientid = XRequest::getValue("patientid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $revisitrecordid = XRequest::getValue("revisitrecordid", 0);

        $reporttplid = XRequest::getValue("reporttplid", 0);

        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            echo "患者不存在";
            return self::BLANK;
        }

        $doctor = Doctor::getById($doctorid);
        if (false == $doctor instanceof Doctor) {
            echo "医生不存在";
            return self::BLANK;
        }

        $pcard = $patient->getPcardByDoctorid($doctor->id);
        if ($pcard->diseaseid != $diseaseid) {
            echo "疾病错误";
            return self::BLANK;
        }

        $reportTpl = ReportTpl::getById($reporttplid);
        if (false == $reportTpl instanceof ReportTpl) {
            echo "汇报模板不存在";
            return self::BLANK;
        }

        $data = [];

        $config = json_decode($reportTpl->content, true);
        foreach ($config as $item) {
            switch ($item) {
                case 'baseInfo': // 基本信息

                    // 诊断
                    $data['pcard'] = $this->getComplication($pcard);

                    // 最近就诊日期
                    $revisitrecord = RevisitRecord::getById($revisitrecordid);
                    if ($revisitrecord instanceof RevisitRecord) {
                        $data['revisitrecord'] = [
                            "revisitrecordid" => $revisitrecord->id,
                            "thedate" => $revisitrecord->thedate];
                    }
                    break;
                case 'patientRemark': // 症状体征及不良反应
                    $data["patientremarks"] = $this->getPatientRemarks($doctorid, $patientid);
                    break;
                case 'patientmedicinepkg': // 用药
                    $data['patientmedicinepkg'] = $this->getPatientMedicinePkg($doctorid, $patientid);
                    break;
                case 'checkuptpls': // 检查报告
                    $data['checkuptpls'] = $this->getCheckuptpls($doctor, $patient, $diseaseid);
                    break;
                case 'diagnose': // 诊断和分期
                                 // 最新诊断
                    $data['diagnose'] = $this->getDiagnose($doctor, $patient);
                    // 最新分期
                    $data['staging'] = $this->getStaging($doctor, $patient);
                    break;
                case 'chemo': // 最新化疗方案
                    $data['chemo'] = $this->getChemo($doctor, $patient);
                    break;
                case 'nexthualiaodate': // 下次诊疗日期
                    $data['nexthualiaodate'] = $this->getNextHualiaoDate($doctor, $patient);
                    break;
                case 'wbc_checkup': // 近期血常规
                    $data['wbc_checkup'] = $this->getWbcCheckup($doctor, $patient);
                    break;
            }
        }

        $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $replaceMap = ['&#60;' => '<', '&#62;' => '>'];
        foreach ($replaceMap as $key => $code) {
            $data_json = str_replace($key, $code, $data_json);
        }

        $row = [];
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["diseaseid"] = $diseaseid;
        $row["appeal"] = $appeal;
        $row["remark"] = $remark;
        $row["data_json"] = $data_json;
        $row["issend"] = 0;
        $report = Report::createByBiz($row);

        $pictures = Dao::getEntityListByIds("Picture", $pictureids);
        foreach ($pictures as $picture) {
            $prow = [];
            $prow["reportid"] = $report->id;
            $prow["pictureid"] = $picture->id;
            $reportPicture = ReportPicture::createByBiz($prow);
        }

        $this->sendTplMsgToDoctor($doctor, $report);

        // 生成任务: 汇报跟进任务
        $plantime = date("Y-m-d", strtotime('+1 day')) . " 10:00:00";
        $optask = OpTaskService::createPatientOpTask($patient, 'follow:Report', $report, $plantime, $this->myauditor->id);

        echo "ok";

        return self::BLANK;
    }

    // 诊断
    private function getComplication ($pcard) {
        $result = [
            'id' => $pcard->id,
            'complication' => $pcard->complication];

        return $result;
    }

    // 症状体征及不良反应
    private function getPatientRemarks ($doctorid, $patientid) {
        $result = [];

        $cond = " patientid = :patientid AND doctorid = :doctorid ";

        $sql = "SELECT pr.*
            FROM (SELECT *
            FROM patientremarks
            WHERE {$cond}
            ORDER BY thedate DESC) pr
            GROUP BY pr.name
            ORDER BY pr.thedate DESC;";

        $bind = [
            ':patientid' => $patientid,
            ':doctorid' => $doctorid];

        $patientRemarks = Dao::loadEntityList('PatientRemark', $sql, $bind);
        foreach ($patientRemarks as $patientRemark) {
            if ($patientRemark->content == '') {
                continue;
            }
            $result[] = [
                'name' => $patientRemark->name,
                'thedate' => $patientRemark->thedate,
                'content' => $patientRemark->content];
        }

        return $result;
    }

    // 用药
    private function getPatientMedicinePkg ($doctorid, $patientid) {
        $result = [];
        $pmtargets = PatientMedicineTargetDao::getListByPatientIdAndDoctorId($patientid, $doctorid);
        if ($pmtargets) {
            $result['thedate'] = $pmtargets[0]->getCreateDay();
            $items = [];
            foreach ($pmtargets as $pmtarget) {
                $items[] = [
                    'medicinename' => $pmtarget->medicine->name,
                    'drug_dose' => $pmtarget->drug_dose,
                    'drug_frequency' => $pmtarget->drug_frequency,
                    'drug_change' => $pmtarget->drug_change];
            }
            $result['items'] = $items;
        }

        return $result;
    }

    // 检查报告
    private function getCheckuptpls ($doctor, $patient, $diseaseid) {
        $result = [];
        $checkuptpls = CheckupTplDao::getListByDoctorAndDiseaseid_isInTkt_isInAdmin($doctor, $diseaseid, null, 1);
        foreach ($checkuptpls as $checkuptpl) {
            $question_arr = [];
            $questions = $checkuptpl->xquestionsheet->getQuestions();
            foreach ($questions as $i => $q) {
                if ($q->isMultText()) {
                    foreach ($q->getMultTitles() as $t) {
                        $question_arr[] = "{$q->content}-{$t}";
                    }
                } else {
                    $question_arr[] = $q->content;
                }
            }

            $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $checkuptpl);
            if (empty($checkups)) {
                continue;
            }
            $checkup_arr = [];
            foreach ($checkups as $checkup) {
                if ($checkup->xanswersheetid == 0) {
                    continue;
                }
                $answer_arr = [];
                foreach ($questions as $i => $q) {
                    $xanswer = $checkup->xanswersheet->getAnswer($q->id);
                    // 有答案
                    if ($xanswer instanceof XAnswer) {
                        foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                            $answer_arr[] = $t;
                        }
                    } else {
                        if ($q->isMultText()) {
                            foreach ($q->getMultTitles() as $t) {
                                $answer_arr[] = '';
                            }
                        } else {
                            $answer_arr[] = '';
                        }
                    }
                }
                $checkup_arr[] = [
                    'id' => $checkup->id,
                    'check_date' => $checkup->check_date,
                    'answers' => $answer_arr];
            }
            if (empty($checkup_arr)) {
                continue;
            }

            $result[] = [
                'id' => $checkuptpl->id,
                'title' => $checkuptpl->title,
                'questions' => $question_arr,
                'checkups' => $checkup_arr];
        }
        return $result;
    }

    // 获取最新诊断
    private function getDiagnose ($doctor, $patient) {
        // 最新诊断
        $diagnosePatientRecord = Dao::getEntityByCond('PatientRecord',
                ' AND patientid=:patientid AND code="cancer" AND type="diagnose" ORDER BY thedate DESC LIMIT 1',
                [
                    ':patientid' => $patient->id]);
        $content = $diagnosePatientRecord->loadJsonContent();
        $data = [];
        $data['thedate'] = $content['thedate'];
        $data['position'] = $content['position'];
        $data['diagnose_start'] = $content['diagnose_start'];
        $data['special'] = $content['special'];
        return $data;
    }

    // 获取最新分期
    private function getStaging ($doctor, $patient) {
        // 最新诊断
        $stagingPatientRecord = Dao::getEntityByCond('PatientRecord',
                ' AND patientid=:patientid AND code="cancer" AND type="staging" ORDER BY thedate DESC LIMIT 1',
                [
                    ':patientid' => $patient->id]);
        $content = $stagingPatientRecord->loadJsonContent();
        $data = [];
        $data['thedate'] = $content['thedate'];
        $data['type'] = $content['type'];
        $data['T'] = $content['T'];
        $data['N'] = $content['N'];
        $data['M'] = $content['M'];
        $data['stage'] = $content['stage'];
        $data['content'] = $stagingPatientRecord->content;
        return $data;
    }

    // 获取最新化疗方案数据
    private function getChemo ($doctor, $patient) {
        $chemoPatientRecord = Dao::getEntityByCond('PatientRecord',
                ' AND patientid=:patientid AND code="cancer" AND type="chemo" ORDER BY thedate DESC LIMIT 1',
                [
                    ':patientid' => $patient->id]);
        $content = $chemoPatientRecord->loadJsonContent();
        $data = [];
        $data['thedate'] = $chemoPatientRecord->thedate;
        $data['protocol'] = $content['protocol'];
        $data['cycle'] = $content['cycle'];
        $data['property'] = $content['property'];
        $data['period'] = $content['period'];
        $data['content'] = $chemoPatientRecord->content;
        return $data;
    }

    // 计算预计下次化疗日期
    private function getNextHualiaoDate ($doctor, $patient) {
        $chemoPatientRecord = Dao::getEntityByCond('PatientRecord',
                ' AND patientid=:patientid AND code="cancer" AND type="chemo" ORDER BY thedate DESC LIMIT 1',
                [
                    ':patientid' => $patient->id]);
        $nextHualiaoDate = '';
        // 如果没有化疗或最新一次的方案日期+周数距今超过一周则不展示该模块
        if ($chemoPatientRecord) {
            $content = $chemoPatientRecord->loadJsonContent();
            $week = 3;
            if ($content['cycle'] == '两周方案') {
                $week = 2;
            } else
                if ($content['cycle'] == '三周方案') {
                    $week = 3;
                } else
                    if ($content['cycle'] == '四周方案') {
                        $week = 4;
                    }
            //
            $t = strtotime($chemoPatientRecord->thedate);
            $now = strtotime(date("Y-m-d"));
            if ($t + $week * 7 * 86400 < $now) {} else {
                $nextHualiaoDate = date('Y-m-d', strtotime("+{$week}week", $t));
            }
        }
        return $nextHualiaoDate;
    }

    // 获取最新血常规数据
    private function getWbcCheckup ($doctor, $patient) {
        $wbcCheckupPatientRecord = Dao::getEntityByCond('PatientRecord',
                ' AND patientid=:patientid AND code="cancer" AND type="wbc_checkup" ORDER BY thedate DESC LIMIT 1',
                [
                    ':patientid' => $patient->id]);
        $content = $wbcCheckupPatientRecord->loadJsonContent();
        $data = [];
        $data['thedate'] = $wbcCheckupPatientRecord->thedate;
        $data['baixibao'] = $content['baixibao'];
        $data['xuehongdanbai'] = $content['xuehongdanbai'];
        $data['xuexiaoban'] = $content['xuexiaoban'];
        $data['zhongxingli'] = $content['zhongxingli'];
        $data['content'] = $wbcCheckupPatientRecord->content;
        return $data;
    }

    // 发送消息给医生
    private function sendTplMsgToDoctor ($doctor, $report) {
        $first = [
            "value" => "尊敬的{$doctor->name}医生，您的助理向您发送了一条重要的汇报消息，请您点击此处及时查看处理",
            "color" => "#3366ff"];

        $date = date("Y-m-d");

        $keywords = [
            [
                "value" => $date,
                "color" => ""],
            [
                "value" => '1',
                "color" => ""]];

        $content = WxTemplateService::createTemplateContent($first, $keywords);

        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/report/{$report->id}/one";
        $result = Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor, $this->myauditor, "PatientMgrNotice", $content, $url);

        // if (!empty($result)) {
        // $report->issend = 1;
        // }
    }

    public function doListByPatient () {
        $patientid = XRequest::getValue("patientid", 0);
        DBC::requireNotEmpty($patientid, 'patientid is null');

        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            DBC::requireNotEmpty($patientid, 'patientid not found');
        }

        $reportid = XRequest::getValue("reportid", 0);

        $reports = ReportDao::getListByPatientid($patientid);

        XContext::setValue('patient', $patient);
        XContext::setValue('reports', $reports);
        XContext::setValue('reportid', $reportid);

        return self::SUCCESS;
    }

    public function doAjaxOne () {
        $reportid = XRequest::getValue("reportid", 0);
        DBC::requireNotEmpty($reportid, 'reportid is null');

        $report = Report::getById($reportid);
        DBC::requireTrue($report instanceof Report, 'report not found');

        $reportpictures = $report->getReportPictures();

        XContext::setValue('report', $report);
        XContext::setValue('reportpictures', $reportpictures);

        return self::SUCCESS;
    }

    public function doAjaxPatientPictures () {
        $patientid = XRequest::getValue("patientid");
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Patient::getById($patientid);
        DBC::requireTrue($patient instanceof Patient, 'patient not found');

        $doctorid = XRequest::getValue("doctorid", 0);
        DBC::requireNotEmpty($doctorid, 'doctorid is null');
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, 'doctor not found');

        $patientPictures = PatientPictureDao::getListByPatientidAndDoctorid($patientid, $doctorid);

        $groups = [];
        foreach ($patientPictures as $patientPicture) {
            $groups[$patientPicture->objtype][] = $patientPicture;
        }

        XContext::setValue('groups', $groups);
        return self::SUCCESS;
    }
}
