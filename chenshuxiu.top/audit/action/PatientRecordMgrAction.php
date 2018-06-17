<?php

// PatientRecordMgrAction
class PatientRecordMgrAction extends AuditBaseAction
{

    public function doList () {
        $patientid = XRequest::getValue('patientid', 0);
        $code = XRequest::getValue('code', 'common');
        $type = XRequest::getValue('type', 'other');

        $cond = " AND patientid = :patientid AND code = :code AND type = :type AND parent_patientrecordid = 0 ORDER BY createtime DESC ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':code'] = $code;
        $bind[':type'] = $type;

        $patientrecords = Dao::getEntityListByCond("PatientRecord", $cond, $bind);
        XContext::setValue("patientrecords", $patientrecords);

        XContext::setValue('code', $code);
        XContext::setValue('type', $type);

        return self::SUCCESS;
    }

    public function doAddChild () {
        $parent_patientrecordid = XRequest::getValue('parent_patientrecordid', 0);
        $parent_patientrecord = PatientRecord::getById($parent_patientrecordid);
        if (false == $parent_patientrecord instanceof PatientRecord) {
            $this->returnError('运营备注不存在');
        }

        XContext::setValue("parent_patientrecord", $parent_patientrecord);
        XContext::setValue("parent_patientrecord_data", $parent_patientrecord->loadJsonContent());
        return self::SUCCESS;
    }

    public function doModifyChild () {
        $patientrecordid = XRequest::getValue('patientrecordid', 0);
        $patientrecord = PatientRecord::getById($patientrecordid);
        if (false == $patientrecord instanceof PatientRecord) {
            $this->returnError('跟进记录不存在');
        }

        if (false == $patientrecord->parent_patientrecord instanceof PatientRecord) {}

        XContext::setValue("patientrecord", $patientrecord);
        XContext::setValue("patientrecord_data", $patientrecord->loadJsonContent());

        return self::SUCCESS;
    }

    public function doModifyChildJson () {
        $patientrecordid = XRequest::getValue('patientrecordid', 0);
        $thedate = XRequest::getValue('thedate', date("Y-m-d"));
        $content = XRequest::getValue('content', '');

        $patientrecord = PatientRecord::getById($patientrecordid);
        if (false == $patientrecord instanceof PatientRecord) {
            $this->returnError('跟进记录不存在');
        }

        $requests = XRequest::getValue($patientrecord->type, []);

        $patientrecord->thedate = $thedate;
        $patientrecord->content = $content;
        $patientrecord->modify_auditorid = $this->myauditor->id;

        $patientrecord->saveJsonContent($requests);

        return self::TEXTJSON;
    }

    public function doModify () {
        $patientrecordid = XRequest::getValue('patientrecordid', 0);
        $patientrecord = PatientRecord::getById($patientrecordid);

        XContext::setValue("patientrecord", $patientrecord);
        XContext::setValue("patientrecord_data", $patientrecord->loadJsonContent());

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $patientrecordid = XRequest::getValue('patientrecordid', 0);
        $thedate = XRequest::getValue('thedate', date("Y-m-d"));
        $content = XRequest::getValue('content', '');

        $patientrecord = PatientRecord::getById($patientrecordid);

        $requests = XRequest::getValue($patientrecord->type, []);

        $patientrecord->modify_auditorid = $this->myauditor->id;

        $content_log = "【<br>";
        if ($patientrecord->thedate != $thedate) {
            $content_log .= " 修改了 [运营备注({$patientrecord->id})] [thedate]:[{$patientrecord->thedate}] => [{$thedate}] <br>";
            $patientrecord->thedate = $thedate;
        }
        if ($patientrecord->content != $content) {
            $content_log .= " 修改了 [运营备注({$patientrecord->id})] [content]:[{$patientrecord->content}] => [{$content}] <br>";
            $patientrecord->content = $content;
        }

        $json_content = json_encode($requests, JSON_UNESCAPED_UNICODE);
        if ($patientrecord->json_content != $json_content) {
            $content_log .= " 修改了 [运营备注({$patientrecord->id})] [json_content]:<br>[{$patientrecord->json_content}] <br> => <br> [{$json_content}] <br>";

            $patientrecord->saveJsonContent($requests);
        }
        $content_log .= "】<br>";

        // 操作日志
        $row = [];
        $row['auditorid'] = $this->myauditor->id;
        $row['patientid'] = $patientrecord->patientid;
        $row['code'] = 'patientrecord';
        $row['content'] = $content_log;
        // 异步创建操作日志
        AuditorOpLog::nsqPush($row);

        XContext::setJumpPath("/patientrecordmgr/modify?patientrecordid={$patientrecordid}&preMsg=" . urlencode('修改已保存'));

        return self::BLANK;
    }

    public function doAddJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $parent_patientrecordid = XRequest::getValue('parent_patientrecordid', 0);
        $type = XRequest::getValue('type', 'other');
        $code = XRequest::getValue('code', 'common');
        $thedate = XRequest::getValue('thedate', date("Y-m-d"));
        $content = XRequest::getValue('content', '');

        $requests = XRequest::getValue($type, []);

        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            $this->returnError('患者不存在');
        }

        // 患者死亡
        if ($type == 'dead') {
            if (! $patient->is_live) {
                $this->returnError('没有[死亡]备注，但是已经被设置为死亡，请联系技术人员');
            }
            $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($patientid, 'common', 'dead');
            if (count($patientrecords) > 0) {
                $this->returnError('[死亡]备注已存在');
            }
            // 设置患者死亡状态
            PatientStatusService::auditor_dead($patient, $this->myauditor, "运营添加[死亡]备注");

            // 如果患者死亡，关闭患者所有未关闭是的任务()
            OpTaskService::closeAllOpTasksOfPatient($patient, $this->myauditor->id);
            BeanFinder::get("UnitOfWork")->commitAndInit();
        } elseif ($type == 'lose') {
            // 患者失访
            $patient->lose();
        }

        $row = [];
        $row["patientid"] = $patientid;
        $row["parent_patientrecordid"] = $parent_patientrecordid;
        $row["type"] = $type;
        $row["code"] = $code;
        $row["thedate"] = $thedate;
        $row["content"] = $content;
        $row["create_auditorid"] = $this->myauditor->id;
        $patientrecord = PatientRecord::createByBiz($row);
        $patientrecord->saveJsonContent($requests);

        $optask_wbc = null;

        // #4391, 新运营备注中有化疗方案录入时立即生成[化疗方案收集]任务和[治疗前提醒]任务（只应用于肺癌:8）
        // #4993, 当前 肿瘤-肺癌。自动任务使用状态 良好。现在肿瘤（肺癌、胃癌、结直肠癌、其他癌症）全部开启。
        $cancer_diseaseids = Disease::getCancerDiseaseidArray();
        if ($code == 'cancer' && $type == 'chemo' && in_array($patient->diseaseid, $cancer_diseaseids)) {
            // 立即生成[化疗方案收集]任务
            $items = json_decode($patientrecord->json_content, true);
            if ($items['cycle'] == '两周方案') {
                $plantime_chemo_collection = date('Y-m-d', strtotime($thedate) + 86400 * 17);
                $plantime_treat_remind = date('Y-m-d', strtotime($thedate) + 86400 * 11);
                $plantime_reaction_collection = date('Y-m-d', strtotime($thedate) + 86400 * 10);
            } elseif ($items['cycle'] == '三周方案' || $items['cycle'] == '未知周期') {
                $plantime_chemo_collection = date('Y-m-d', strtotime($thedate) + 86400 * 24);
                $plantime_treat_remind = date('Y-m-d', strtotime($thedate) + 86400 * 18);
                $plantime_reaction_collection = date('Y-m-d', strtotime($thedate) + 86400 * 14);
            } elseif ($items['cycle'] == '四周方案') {
                $plantime_chemo_collection = date('Y-m-d', strtotime($thedate) + 86400 * 31);
                $plantime_treat_remind = date('Y-m-d', strtotime($thedate) + 86400 * 25);
                $plantime_reaction_collection = date('Y-m-d', strtotime($thedate) + 86400 * 14);
            } elseif ($items['cycle'] == '六周方案') {
                $plantime_chemo_collection = date('Y-m-d', strtotime($thedate) + 86400 * 45);
                $plantime_treat_remind = date('Y-m-d', strtotime($thedate) + 86400 * 39);
                $plantime_reaction_collection = date('Y-m-d', strtotime($thedate) + 86400 * 14);
            }

            $auditorid = $this->myauditor->id;

            // 生成任务: 化疗方案收集任务 (患者唯一)
            if ($plantime_chemo_collection >= date('Y-m-d')) {
                // 2018-04-02 王宫瑜要求不唯一
                // OpTaskService::tryCreateOpTaskByPatient($patient,
                // 'chemo:collection', $patientrecord,
                // $plantime_chemo_collection, $auditorid);
                OpTaskService::createPatientOpTask($patient, 'chemo:collection', $patientrecord, $plantime_chemo_collection, $auditorid);
            }

            // 生成任务: 肿瘤治疗前提醒任务 (患者唯一)
            if ($plantime_treat_remind >= date('Y-m-d')) {
                // #5612 暂时下线
                // OpTaskService::tryCreateOpTaskByPatient($patient,
            // 'treat:remind', $patientrecord, $plantime_treat_remind,
            // $auditorid);
            }

            // 生成任务: 肿瘤不良反应收集任务 (患者唯一)
            if ($plantime_reaction_collection >= date('Y-m-d')) {
                // #5612 暂时下线
                // OpTaskService::tryCreateOpTaskByPatient($patient,
            // 'reaction:collection', $patientrecord,
            // $plantime_reaction_collection, $auditorid);
            }

            // [血常规收集]任务
            // 任务日期:化疗日期7天
            $plantime_wbc_collection = date('Y-m-d', strtotime($thedate) + 86400 * 7);
            // 10天前方案,不再生成任务
            $plantime_wbc_collection_to = date('Y-m-d', strtotime($thedate) + 86400 * 10);

            // 生成任务: 血常规收集任务 (患者唯一)
            if ($plantime_wbc_collection_to >= date('Y-m-d')) {
                $optask_wbc = OpTaskService::tryCreateOpTaskByPatient($patient, 'wbc:collection', $patientrecord, $plantime_wbc_collection, $auditorid);
            }
        }

        // 操作日志
        $content = "【添加了 [运营备注] [{$patientrecord->getTitle()}({$patientrecord->id})] 】<br>";
        if ($optask_wbc instanceof OpTask) {
            $content .= "【添加了 [{$optask_wbc->optasktpl->title}({$optask_wbc->id})({$optask_wbc->plantime})] 任务 】<br>";
        }
        $row = [];
        $row['auditorid'] = $this->myauditor->id;
        $row['patientid'] = $patient->id;
        $row['code'] = 'patientrecord';
        $row['content'] = $content;
        // 异步创建操作日志
        AuditorOpLog::nsqPush($row);

        return self::TEXTJSON;
    }

    public function doDeleteJson () {
        $patientrecordid = XRequest::getValue('patientrecordid', '');
        DBC::requireNotEmpty($patientrecordid, 'patientrecordid is null');

        $patientrecord = Dao::getEntityById('PatientRecord', $patientrecordid);
        DBC::requireNotEmpty($patientrecord, 'patientrecord is null');

        if ($patientrecord->parent_patientrecordid == 0) {
            $children = $patientrecord->getChildren();
            if (! empty($children)) {
                $this->returnError('必须先删除该备注下的跟进记录');
            }
        }
        $patientrecord->remove();

        // 操作日志
        $content = "【删除了 [运营备注] [{$patientrecord->getTitle()}({$patientrecord->id})] 】<br>";
        $row = [];
        $row['auditorid'] = $this->myauditor->id;
        $row['patientid'] = $patientrecord->patientid;
        $row['code'] = 'patientrecord';
        $row['content'] = $content;
        // 异步创建操作日志
        AuditorOpLog::nsqPush($row);

        return self::TEXTJSON;
    }

    public function doListHtmlOfADHD () {
        $patientid = XRequest::getValue("patientid", 0);
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patientrecordtplid = XRequest::getValue("patientrecordtplid", 0);

        $patient = Patient::getById($patientid);
        $diseaseGroup = $patient->disease->diseasegroup;
        $patientRecordTpls = PatientRecordTplDao::getIsShowListByDiseaseGroup($diseaseGroup);

        if ($patientrecordtplid > 0) {
            $patientRecords = PatientRecordDao::getListByPatientidPatientRecordTplid($patientid, $patientrecordtplid, false);
        } else {
            $patientRecords = PatientRecordDao::getListByPatientid($patientid, false);
        }

        XContext::setValue("patientRecordTpls", $patientRecordTpls);
        XContext::setValue("patientrecordtplid", $patientrecordtplid);

        XContext::setValue("patientRecords", $patientRecords);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doAddJsonOfADHD () {
        $patientid = XRequest::getValue('patientid', 0);
        DBC::requireTrue($patientid > 0, "patientid不能为空");

        $data = XRequest::getValue('data', array());
        DBC::requireTrue(count($data) > 0, "提交数据不能为空");

        $patient = Patient::getById($patientid);

        $this->result['errno'] = 0;
        $this->result['errmsg'] = 'ok';
        $this->result['data'] = '';

        foreach ($data as $a) {
            $row = [];
            $row["patientid"] = $patient->id;
            $row["patientrecordtplid"] = $a["patientrecordtplid"];
            $row["content"] = $a["content"];
            $row["thedate"] = date("Y-m-d");
            $row["create_auditorid"] = $this->myauditor->id;
            $row["modify_auditorid"] = $this->myauditor->id;
            $patientrecord = PatientRecord::createByBiz($row);

            // 操作日志
            $content = "【添加了 [运营备注] [({$patientrecord->id})] 】<br>";
            $row = [];
            $row['auditorid'] = $this->myauditor->id;
            $row['patientid'] = $patient->id;
            $row['code'] = 'patientrecord';
            $row['content'] = $content;
            // 异步创建操作日志
            AuditorOpLog::nsqPush($row);
        }

        return self::TEXTJSON;
    }

    public function doModifyJsonOfADHD () {
        $patientrecordid = XRequest::getValue('patientrecordid', 0);
        DBC::requireTrue($patientrecordid > 0, "patientrecordid不能为空");
        $content = XRequest::getValue('content', '');
        DBC::requireTrue($content != '', "content不能为空");

        $patientRecord = PatientRecord::getById($patientrecordid);
        $patientRecord->content = $content;
        $patientRecord->modify_auditorid = $this->myauditor->id;

        $this->result['errno'] = 0;
        $this->result['errmsg'] = 'ok';
        $this->result['data'] = '';
        return self::TEXTJSON;
    }
}
