<?php

// PmSideEffectMgrAction
class PmSideEffectMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        $patientid = XRequest::getValue('patientid', 0);
        $patient_name = XRequest::getValue('patient_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        XContext::setValue('patientid', $patientid);
        XContext::setValue('patient_name', $patient_name);

        $medicineid = XRequest::getValue("medicineid", 0);
        $fromdate = XRequest::getValue("fromdate", '');
        $todate = XRequest::getValue("todate", '');

        $cond = '';
        $bind = [];

        if ($doctorid > 0) {
            $cond .= ' and doctorid=:doctorid ';
            $bind[":doctorid"] = $doctorid;
        }

        if ($patientid > 0) {
            $cond .= ' and patientid=:patientid ';
            $bind[":patientid"] = $patientid;
        }

        if ($medicineid > 0) {
            $cond .= ' and medicineid=:medicineid ';
            $bind[":medicineid"] = $medicineid;
        }

        if ($patient_name) {
            $cond .= " and patientid in ( select distinct patientid from xpatientindexs where word = :word  ) ";
            $bind[":word"] = $patient_name;
        }

        if ($fromdate) {
            $cond .= ' and thedate >= :thedate ';
            $bind[":thedate"] = $fromdate;
        }

        if ($todate) {
            $cond .= ' and thedate <= :thedate ';
            $bind[":thedate"] = $todate;
        }

        $cond .= ' order by id desc ';

        $pmsideeffects = Dao::getEntityListByCond4Page("PmSideEffect", $pagesize, $pagenum, $cond, $bind);

        XContext::setValue("patient_name", $patient_name);
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("pmsideeffects", $pmsideeffects);

        $countSql = "select count(*) as cnt from pmsideeffects where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/pmsideeffectmgr/list";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    public function doAdd () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);

        XContext::setValue('patient', $patient);
        return self::SUCCESS;
    }

    // 修改提交
    public function doAddPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $medicineid = XRequest::getValue("medicineid", 0);
        $plantime = XRequest::getValue("plantime", "0000-00-00");

        $patient = Patient::getById($patientid);

        // 根据任务模板上的疾病, 来判断是否创建副反应监控?
        $optasktpl = OpTaskTplDao::getOneByUnicode('remind:PmSideEffect');

        $diseaseids = explode(',', $optasktpl->diseaseids);
        if (!in_array($patient->diseaseid, $diseaseids)) {
            XContext::setJumpPath("/pmsideeffectmgr/add?patientid={$patient->id}&preMsg=" . urlencode("方寸儿童管理服务平台方向不能创建药物副反应"));
            return self::SUCCESS;
        }

        $row = array();
        $row["patientid"] = $patientid;
        $row["doctorid"] = $patient->doctorid;
        $row["medicineid"] = $medicineid;

        // 药品暂时可以不存在
        $medicine = Medicine::getById($medicineid);
        // DBC::requireTrue($medicine instanceof Medicine,
        // "检测药品[{$medicineid}]不存在, 请重新选择药物");

        DBC::requireTrue($plantime != '0000-00-00', "任务plantime[{$plantime}]为空, 请重新选择日期");

        // 副反应检测的结果
        $pmSideEffect = PmSideEffect::createByBiz($row);

        // 生成任务: 药物副反应检查跟进
        $optask = OpTaskService::createPatientOpTask($patient, 'remind:PmSideEffect', $pmSideEffect, $plantime, $this->myauditor->id);

        if ($optask instanceof OpTask) {
            // 如果plantime = 明天 ，则立即发送 药物副反应检测
            if ($optask->getPlanDate() == date('Y-m-d', time() + 3600 * 24)) {
                PmSideEffectService::sendOpTaskPmRemid($optask);
            }
        }

        $preMsg = "已添加 " . XDateTime::now();

        XContext::setJumpPath("/pmsideeffectmgr/modify?pmsideeffectid={$pmSideEffect->id}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doModify () {
        $pmsideeffectid = XRequest::getValue("pmsideeffectid", 0);

        $pmsideeffect = PmSideEffect::getById($pmsideeffectid);

        $picturerefs = PictureRefDao::getListByObj($pmsideeffect);
        $wxpicmsgs = WxPicMsgDao::getListByPatientid($pmsideeffect->patientid);

        XContext::setValue("pmsideeffect", $pmsideeffect);
        XContext::setValue('picturerefs', $picturerefs);
        XContext::setValue('wxpicmsgs', $wxpicmsgs);
        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $pmsideeffectid = XRequest::getValue("pmsideeffectid", 0);
        $thedate = XRequest::getValue("thedate", "0000-00-00");
        $medicineid = XRequest::getValue("medicineid", 0);
        $content = XRequest::getValue("content", "");

        $pmsideeffect = PmSideEffect::getById($pmsideeffectid);
        $pmsideeffect->thedate = $thedate;
        $pmsideeffect->medicineid = $medicineid;
        $pmsideeffect->content = $content;

        $preMsg = "修改已提交 " . XDateTime::now();

        XContext::setJumpPath("/pmsideeffectmgr/modify?pmsideeffectid={$pmsideeffectid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doAuditJson () {
        $pmsideeffectid = XRequest::getValue("pmsideeffectid", 0);
        $result_status = XRequest::getValue("result_status", '');

        $pmsideeffect = PmSideEffect::getById($pmsideeffectid);

        $pmsideeffect->thedate = date('Y-m-d');
        $pmsideeffect->result_status = $result_status;

        echo 'success';

        return self::BLANK;
    }
}
