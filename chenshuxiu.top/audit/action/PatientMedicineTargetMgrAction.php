<?php

// PatientMedicinePkgItemMgrAction
class PatientMedicineTargetMgrAction extends AuditBaseAction
{

    public function doDetailOfPatient () {
        $patientid = XRequest::getValue('patientid', 0);
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, 'patient is null');
        $cond = ' AND patientid=:patientid AND doctorid=:doctorid';
        $bind = [
            ':patientid' => $patientid,
            ':doctorid' => $patient->doctorid];
        $pmTargets = Dao::getEntityListByCond('PatientMedicineTarget', $cond, $bind);

        $pmsheets = PatientMedicineSheetDao::getListByPatient($patient);
        XContext::setValue('pmsheets', $pmsheets);

        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid
            ORDER BY b.medicineid DESC, b.drug_date DESC";
        $pmsitems = Dao::loadEntityList('PatientMedicineSheetItem', $sql, $bind);
        $allpmsitems = [];
        foreach ($pmsitems as $pmsitem) {
            $allpmsitems[$pmsitem->medicine->name][] = $pmsitem;
        }

        XContext::setValue('allpmsitems', $allpmsitems);

        $pmpkgs = PatientMedicinePkgDao::getListByPatientid($patient->id);
        XContext::setValue('pmpkgs', $pmpkgs);

        // 去openid是为了预览用药审核页面
        $wxuser = $patient->getMasterWxUser();
        XContext::setValue('openid', $wxuser->openid);

        XContext::setValue('patient', $patient);
        XContext::setValue('pmTargets', $pmTargets);

        return self::SUCCESS;
    }

    // 添加应用药模板
    public function doAddStandardMedicineHtml () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Dao::getEntityById('Patient', $patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();

        XContext::setValue('patient', $patient);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    // 添加应用药提交
    public function doAddStandardMedicineJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Dao::getEntityById('Patient', $patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');
        $auditremark = XRequest::getValue('auditremark', '');
        $medicineid = XRequest::getValue('medicineid', '');
        $record_date = XRequest::getValue('record_date', date('Y-m-d'));

        $medicine = Dao::getEntityById('Medicine', $medicineid);
        DBC::requireNotEmpty($medicine, 'medicine is null');

        $cond = " AND medicineid=:medicineid AND patientid=:patientid AND doctorid=:doctorid";
        $bind = [
            ':medicineid' => $medicineid,
            ':patientid' => $patientid,
            ':doctorid' => $patient->doctorid];
        $pmtarget = Dao::getEntityByCond('PatientMedicineTarget', $cond, $bind);
        if ($pmtarget) {
            echo '应用药中已存在该药';
            return self::BLANK;
        }

        $fiveIds = $patient->get5Id();
        $row = [];
        $row['wxuserid'] = $fiveIds['wxuserid'];
        $row['userid'] = $fiveIds['userid'];
        $row['patientid'] = $patient->id;
        $row['doctorid'] = $patient->doctorid;
        $row['medicineid'] = $medicineid;
        $row['drug_dose'] = $drug_dose;
        $row['drug_frequency'] = $drug_frequency;
        $row['drug_change'] = $drug_change;
        $row['createby'] = 'Auditor';
        $row['auditremark'] = $auditremark;
        $row['record_date'] = $record_date;

        $pmTarget = PatientMedicineTarget::createByBiz($row);

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        // $pcard = $patient->getPcardByDoctorid($patient->doctorid);
        // PADRMonitorService::updateMonitorByAuditorForTarget($patient,
        // $pcard->diseaseid, $pmTarget);

        echo 'ok';
        return self::BLANK;
    }

    public function doAddMedicineHtml () {
        $pmtargetid = XRequest::getValue('pmtargetid', '');
        DBC::requireNotEmpty($pmtargetid, 'pmtargetid is null');
        $pmtarget = Dao::getEntityById('PatientMedicineTarget', $pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');

        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();

        XContext::setValue('pmtarget', $pmtarget);
        XContext::setValue('patient', $pmtarget->patient);
        XContext::setValue('medicine', $pmtarget->medicine);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    public function doAddHistoryMedicineHtml () {
        $patientid = XRequest::getValue('patientid', '');
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Dao::getEntityById('Patient', $patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $doctorid = XRequest::getValue('doctorid', '');
        DBC::requireNotEmpty($doctorid, 'doctorid is null');

        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();

        XContext::setValue('patient', $patient);
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    public function doStopMedicineHtml () {
        $pmtargetid = XRequest::getValue('pmtargetid', '');
        DBC::requireNotEmpty($pmtargetid, 'pmtargetid is null');
        $pmtarget = Dao::getEntityById('PatientMedicineTarget', $pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');

        XContext::setValue('pmtarget', $pmtarget);
        XContext::setValue('patient', $pmtarget->patient);
        XContext::setValue('medicine', $pmtarget->medicine);
        return self::SUCCESS;
    }

    // 修改医嘱用药
    public function doModifyMedicineHtml () {
        $pmtargetid = XRequest::getValue('pmtargetid', '');
        DBC::requireNotEmpty($pmtargetid, 'pmtargetid is null');
        $pmtarget = Dao::getEntityById('PatientMedicineTarget', $pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');

        $drug_frequency_arr = Medicine::get_drug_frequency_Arr_define();

        XContext::setValue('pmtarget', $pmtarget);
        XContext::setValue('patient', $pmtarget->patient);
        XContext::setValue('medicine', $pmtarget->medicine);
        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        return self::SUCCESS;
    }

    // 新增实际用药实际是造一条pmsitem
    public function doAddMedicineJson () {
        $pmtargetid = XRequest::getValue('pmtargetid', '');
        DBC::requireNotEmpty($pmtargetid, 'pmtargetid is null');
        $pmtarget = Dao::getEntityById('PatientMedicineTarget', $pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');

        $record_date = XRequest::getValue('record_date', '');
        $medicineid = XRequest::getValue('medicineid', '');
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $auditremark = XRequest::getValue('auditremark', '');
        $status = XRequest::getValue('status', 0);

        $medicine = Medicine::getById($medicineid);

        $thedate = date('Y-m-d');
        $pmsheet = PatientMedicineSheetDao::getByPatientThedate($pmtarget->patient, $thedate);
        $fiveIds = $pmtarget->patient->get5Id();
        // 不存在thedate 的pmsheet 需要创建一个
        if (! $pmsheet) {
            $row = [];
            $row['wxuserid'] = $fiveIds['wxuserid'];
            $row['userid'] = $fiveIds['userid'];
            $row['patientid'] = $pmtarget->patientid;
            $row['doctorid'] = $pmtarget->doctorid;
            // 对于手工添加的这个时间不准确,因为手工添加是一个个添加不是一批提交
            $row['thedate'] = date('Y-m-d');
            $row['auditorid'] = $this->myauditor->id;
            $row['auditstatus'] = 1;
            $row['content'] = '';
            $row['createby'] = 'Auditor';
            $pmsheet = PatientMedicineSheet::createByBiz($row);

            // 入流
            $pipe = Pipe::createByEntity($pmsheet);
        }

        // 创建一条pmsitem为用药记录 可能会挂到患者刚提交还未核对的的pms上，此时以运营提交实际用药为准
        // 也有可能挂到已经核对后的pms上
        // 也可能新创建一个pmsheet
        $row = [];
        $row['patientmedicinesheetid'] = $pmsheet->id;
        $row['medicineid'] = $medicineid;
        $row['status'] = $status; // 正常用药
        $row['auditorid'] = $this->myauditor->id;
        $row['drug_date'] = $record_date;
        $row['createby'] = 'Auditor';
        $row['auditlog'] = '';
        $row['auditremark'] = $auditremark;
        $row['drug_dose'] = $drug_dose;
        $row['drug_frequency'] = $drug_frequency;
        $row['target_drug_dose'] = $pmtarget->drug_dose;
        $row['target_drug_frequency'] = $pmtarget->drug_frequency;
        $pmsitem = PatientMedicineSheetItem::createByBiz($row);

        // 这里是为了兼容现有业务代码（将来会全部替换）
        // 修改PatientMedicineRef的最新和最早用药时间，同时更新最新状态
        $patientmedicineref = $pmtarget->patient->getRefWithMedicine($medicine, true);
        // 除了停药以外其他都是正常服药
        $pmref_status = $status == 3 ? 0 : $status;
        // 新生成的
        if ("0000-00-00" == $patientmedicineref->first_start_date) {
            $patientmedicineref->first_start_date = $record_date;
            $patientmedicineref->startdate = $record_date;
            $patientmedicineref->last_drugchange_date = $record_date;
            $patientmedicineref->wxuserid = $fiveIds["wxuserid"];
            $patientmedicineref->userid = $fiveIds["userid"];
            $patientmedicineref->doctorid = $fiveIds["doctorid"];
            $patientmedicineref->status = $pmref_status;
            $patientmedicineref->drug_dose = $drug_dose;
            $patientmedicineref->drug_frequency = $drug_frequency;
        } else {
            if (strtotime($record_date) < strtotime($patientmedicineref->first_start_date)) {
                $patientmedicineref->first_start_date = $record_date;
            }

            // 填写时间大于等于最后变更时间
            if (strtotime($record_date) >= strtotime($patientmedicineref->last_drugchange_date)) {
                $patientmedicineref->status = $pmref_status;
                $patientmedicineref->last_drugchange_date = $record_date;
                $patientmedicineref->drug_dose = $drug_dose;
                $patientmedicineref->drug_frequency = $drug_frequency;
            }
        }

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        $patient = $pmtarget->patient;
        $pcard = PcardDao::getByPatientidDoctorid($patient->id, $pmtarget->doctorid);
        // PADRMonitorService::updateMonitorByAuditorForDrug($patient,
        // $pcard->diseaseid);

        // 靶向药核对任务，用药核对
        $this->patientMedicineCheck($pcard, $patientmedicineref->wxuser);

        echo 'ok';
        return self::BLANK;
    }

    // MARK: - 关闭当前已有的靶向药核对任务（标注为系统关闭）并创建28天后的靶向药核对任务 by likunting
    // 在治疗阶段为靶向药 且 无任何化疗中的治疗阶段时。靶向药核对任务 进入完成、超时、拒绝后增加28天生成。
    private function patientMedicineCheck (Pcard $pcard, $wxuser) {
        $patient = $pcard->patient;
        $cancer_diseaseids = Disease::getCancerDiseaseidArray();
        if (in_array($pcard->diseaseid, $cancer_diseaseids)) { // 目前只部署肺癌,#4719
            $tag_bxy = TagDao::getByName("靶向药");
            $tagRef_bxy = TagRefDao::getByObjtypeObjidTagid("Patient", $patient->id, $tag_bxy->id);
            if ($tagRef_bxy instanceof TagRef) {
                $tag_hlz_arr = TagDao::getListByFuzzyName("化疗中");
                $pass = true;
                foreach ($tag_hlz_arr as $tag) {
                    $tagRef = TagRefDao::getByObjtypeObjidTagid("Patient", $patient->id, $tag->id);
                    if ($tagRef instanceof TagRef) {
                        $pass = false;
                        break;
                    }
                }
                if ($pass) {
                    $row = [];
                    $row["patientid"] = $patient->id;
                    $row["type"] = "targeted_drug";
                    $pmCheck = PatientMedicineCheck::createByBiz($row);

                    // 关闭当前已有的靶向药核对任务（标注为系统关闭）
                    $optask = OpTaskDao::getOneByPatientUnicode($patient, 'patientmedicine:check', true);

                    if ($optask instanceof OpTask) {
                        OpTaskEngine::flow_to_opnode($optask, 'finish', $this->myauditor->id);
                    }
                }
            }
        }
    }

    // 添加历史用药（没有应用药对应的）
    public function doAddHistoryMedicineJson () {
        $patientid = XRequest::getValue('patientid', '');
        DBC::requireNotEmpty($patientid, 'patientid is null');
        $patient = Dao::getEntityById('Patient', $patientid);
        DBC::requireNotEmpty($patient, 'patient is null');

        $doctorid = XRequest::getValue('doctorid', '');
        DBC::requireNotEmpty($doctorid, 'doctorid is null');

        $record_date = XRequest::getValue('record_date', '');
        $medicineid = XRequest::getValue('medicineid', '');
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $auditremark = XRequest::getValue('auditremark', '');
        $status = XRequest::getValue('status', 0);

        $medicine = Medicine::getById($medicineid);

        $thedate = date('Y-m-d');
        $pmsheet = PatientMedicineSheetDao::getByPatientThedate($patient, $thedate);
        $fiveIds = $patient->get5Id();
        // 不存在 thedate 的pmsheet 需要创建一个
        if (! $pmsheet) {
            $row = [];
            $row['wxuserid'] = $fiveIds['wxuserid'];
            $row['userid'] = $fiveIds['userid'];
            $row['patientid'] = $patientid;
            $row['doctorid'] = $doctorid; // done pcard fix
                                          // 对于手工添加的这个时间不准确,因为手工添加是一个个添加不是一批提交
            $row['thedate'] = date('Y-m-d');
            $row['auditorid'] = $this->myauditor->id;
            $row['auditstatus'] = 1;
            $row['content'] = '';
            $row['createby'] = 'Auditor';
            $pmsheet = PatientMedicineSheet::createByBiz($row);

            // 历史用药不入流
            // $pipe = Pipe::createByEntity($pmsheet);
        }

        // 创建一条pmsitem为用药记录 可能会挂到患者刚提交还未核对的的pms上，此时以运营提交实际用药为准
        // 也有可能挂到已经核对后的pms上
        // 也可能新创建一个pmsheet
        $row = [];
        $row['patientmedicinesheetid'] = $pmsheet->id;
        $row['medicineid'] = $medicineid;
        $row['status'] = $status; // 正常用药
        $row['auditorid'] = $this->myauditor->id;
        $row['drug_date'] = $record_date;
        $row['createby'] = 'Auditor';
        $row['auditlog'] = '';
        $row['auditremark'] = $auditremark;
        $row['drug_dose'] = $drug_dose;
        $row['drug_frequency'] = $drug_frequency;
        $row['target_drug_dose'] = '';
        $row['target_drug_frequency'] = '';
        $pmsitem = PatientMedicineSheetItem::createByBiz($row);

        // 这里是为了兼容现有业务代码（将来会全部替换）
        // 修改PatientMedicineRef的最新和最早用药时间，同时更新最新状态
        $patientmedicineref = $patient->getRefWithMedicine($medicine, true);
        // 除了停药以外其他都是正常服药
        $pmref_status = $status == 3 ? 0 : $status;
        // 新生成的
        if ("0000-00-00" == $patientmedicineref->first_start_date) {
            $patientmedicineref->first_start_date = $record_date;
            $patientmedicineref->startdate = $record_date;
            $patientmedicineref->last_drugchange_date = $record_date;
            $patientmedicineref->wxuserid = $fiveIds["wxuserid"];
            $patientmedicineref->userid = $fiveIds["userid"];
            $patientmedicineref->doctorid = $fiveIds["doctorid"];
            $patientmedicineref->status = $pmref_status;
            $patientmedicineref->drug_dose = $drug_dose;
            $patientmedicineref->drug_frequency = $drug_frequency;
        } else {
            if (strtotime($record_date) < strtotime($patientmedicineref->first_start_date)) {
                $patientmedicineref->first_start_date = $record_date;
            }

            // 填写时间大于等于最后变更时间
            if (strtotime($record_date) >= strtotime($patientmedicineref->last_drugchange_date)) {
                $patientmedicineref->status = $pmref_status;
                $patientmedicineref->last_drugchange_date = $record_date;
                $patientmedicineref->drug_dose = $drug_dose;
                $patientmedicineref->drug_frequency = $drug_frequency;
            }
        }

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        // $pcard = $patient->getPcardByDoctorid($doctorid);
        // PADRMonitorService::updateMonitorByAuditorForDrug($patient,
        // $pcard->diseaseid);

        echo 'ok';
        return self::BLANK;
    }

    // 停药实际是造一条pmsitem(实际用药)
    public function doStopMedicineJson () {
        $pmtargetid = XRequest::getValue('pmtargetid', '');
        DBC::requireNotEmpty($pmtargetid, 'pmtargetid is null');
        $pmtarget = Dao::getEntityById('PatientMedicineTarget', $pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');

        $record_date = XRequest::getValue('record_date', '');
        $medicineid = XRequest::getValue('medicineid', '');
        $auditremark = XRequest::getValue('auditremark', '');

        $type = XRequest::getValue('type', '');

        $medicine = Medicine::getById($medicineid);
        DBC::requireNotEmpty($medicine, 'medicine is null');

        $thedate = date('Y-m-d');
        $pmsheet = PatientMedicineSheetDao::getByPatientThedate($pmtarget->patient, $thedate);
        // 如果不存在pmsheet 需要创建一个
        $fiveIds = $pmtarget->patient->get5Id();
        if (! $pmsheet) {
            $row = [];
            $row['wxuserid'] = $fiveIds['wxuserid'];
            $row['userid'] = $fiveIds['userid'];
            $row['patientid'] = $pmtarget->patientid;
            $row['doctorid'] = $pmtarget->doctorid;
            // 对于手工添加的这个时间不准确,因为手工添加是一个个添加不是一批提交
            $row['thedate'] = substr($record_date, 0, 10);
            $row['auditorid'] = $this->myauditor->id;
            $row['auditstatus'] = 1;
            $row['content'] = '';
            $row['createby'] = 'Auditor';

            $pmsheet = PatientMedicineSheet::createByBiz($row);

            // 入流
            $pipe = Pipe::createByEntity($pmsheet);
        }

        // 创建一条pmsitem为停药记录 可能会挂到患者刚提交还未核对的的pms上，此时以运营提交实际用药为准
        $row = [];
        $row['patientmedicinesheetid'] = $pmsheet->id;
        $row['medicineid'] = $medicineid;
        $row['status'] = 3; // 停药
        $row['auditorid'] = $this->myauditor->id;
        $row['drug_date'] = $record_date;
        $row['createby'] = 'Auditor';
        $row['auditlog'] = '';
        $row['auditremark'] = $auditremark;
        $pmsitem = PatientMedicineSheetItem::createByBiz($row);

        // 这里是为了兼容现有业务代码（将来会全部替换）
        // 修改PatientMedicineRef的最新和最早用药时间，同时更新最新状态
        $patientmedicineref = $pmtarget->patient->getRefWithMedicine($medicine, true);
        // 新生成的
        if ("0000-00-00" == $patientmedicineref->first_start_date) {
            $patientmedicineref->first_start_date = $record_date;
            $patientmedicineref->startdate = $record_date;
            $patientmedicineref->last_drugchange_date = $record_date;
            $patientmedicineref->wxuserid = $fiveIds["wxuserid"];
            $patientmedicineref->userid = $fiveIds["userid"];
            $patientmedicineref->doctorid = $fiveIds["doctorid"];
            $patientmedicineref->status = 0;
        } else {
            // 填写时间大于等于最后变更时间
            if (strtotime($record_date) >= strtotime($patientmedicineref->last_drugchange_date)) {
                // 先把状态置成1，应对停药后又新增用药的情况
                $patientmedicineref->last_drugchange_date = $record_date;
                $patientmedicineref->status = 0;
            } else {
                $patientmedicineref->first_start_date = $record_date;
            }
        }

        if ($type == 'stopAndRemove') {
            $pmtarget->remove();
        }

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // #5225 尝试关闭不良反应监测功能
        PADRMonitor_AutoService::tryCloseMonitor($pmtarget->patient);

        echo 'ok';
        return self::BLANK;
    }

    public function doModifyMedicineJson () {
        $pmtargetid = XRequest::getValue('pmtargetid', 0);
        $pmtarget = PatientMedicineTarget::getById($pmtargetid);

        $record_date = XRequest::getValue('record_date', '');
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');
        $auditremark = XRequest::getValue('auditremark', '');

        $pmtarget->record_date = $record_date;
        $pmtarget->drug_dose = $drug_dose;
        $pmtarget->drug_frequency = $drug_frequency;
        $pmtarget->drug_change = $drug_change;
        $pmtarget->auditremark = $auditremark;

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        // $pcard = PcardDao::getByPatientidDoctorid($pmtarget->patientid,
        // $pmtarget->doctorid);
        // PADRMonitorService::updateMonitorByAuditorForDrug($pmtarget->patient,
        // $pcard->diseaseid);

        echo 'ok';
        return self::BLANK;
    }

    public function doDeleteJson () {
        $pmtargetid = XRequest::getValue('pmtargetid', 0);
        $pmtarget = PatientMedicineTarget::getById($pmtargetid);
        DBC::requireNotEmpty($pmtarget, 'pmtarget is null');
        $pmtarget->remove();

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        // $pcard = PcardDao::getByPatientidDoctorid($pmtarget->patientid,
        // $pmtarget->doctorid);
        // PADRMonitorService::updateMonitorByAuditorForDrug($pmtarget->patient,
        // $pcard->diseaseid);

        echo 'ok';
        return self::BLANK;
    }

    // 删除实际用药
    public function doDeletePmsitemJson () {
        $pmsitemid = XRequest::getValue('pmsitemid', 0);
        $pmsitem = PatientMedicineSheetItem::getById($pmsitemid);
        DBC::requireNotEmpty($pmsitem, 'pmsitem is null');

        DBC::requireTrue($pmsitem->createby == 'Auditor', '只能删除运营创建的实际用药');
        $pmsitem->remove();

        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid
            AND b.medicineid=:medicineid
            ORDER BY b.drug_date ASC";
        $bind = [
            ':patientid' => $pmsitem->patientmedicinesheet->patientid,
            ':doctorid' => $pmsitem->patientmedicinesheet->doctorid,
            ':medicineid' => $pmsitem->medicineid];
        $pmsitems = Dao::loadEntityList('PatientMedicineSheetItem', $sql, $bind);

        $patient = $pmsitem->patientmedicinesheet->patient;
        $patientid = $patient->id;
        $medicineid = $pmsitem->medicineid;
        $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($patientid, $medicineid);
        $len = count($pmsitems);
        DBC::requireTrue($len > 0, "一个实际用药都没有，删什么删！？");
        // 这个sheet下的所有items被删尽的时候，pmsheet也给删了，同时对应的pipe流也给删了个球的
        $pmsheet = $pmsitem->patientmedicinesheet;
        $pmsitemsAllMedicine = PatientMedicineSheetItemDao::getListByPatientmedicinesheetid($pmsheet->id, true);
        if (count($pmsitemsAllMedicine) == 1) {
            $pipe = PipeDao::getByEntity($pmsheet);
            if ($pipe instanceof Pipe) {
                $pipe->remove();
            }
            $pmsheet->remove();
        }

        // 倘若这个药的记录被删完了，pmref也要删掉
        if ($len == 1) {
            $patientmedicineref->remove();
        } elseif ($pmsitems[0]->id == $pmsitem->id) { // 首位
            $patientmedicineref->first_start_date = $pmsitems[1]->drug_date;
        } elseif ($pmsitems[$len - 1] == $pmsitem->id) { // 末位
            $patientmedicineref->last_drugchange_date = $pmsitems[$len - 2]->drug_date;
        }

        BeanFinder::get("UnitOfWork")->commitAndInit();

        // MARK: - 生成不良反应监测任务 by likunting
        // $pcard = $patient->getPcardByDoctorid($patientmedicineref->doctorid);
        // PADRMonitorService::updateMonitorByAuditorForDrug($patient,
        // $pcard->diseaseid);

        echo 'ok';
        return self::BLANK;
    }

    // 开启 不良反应监测
    public function doOpenAdr_monitorPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $checkdates = XRequest::getValue('checkdates', []);

        $items = PatientService::getCheckItemsByPatient($patient);

        if (count($checkdates) != count($items)) {
            DBC::requireTrue(false, "下一次日期必填");
        }

        // 打开不良反应监测
        $padrmonitors = PADRMonitor_AutoService::openMonitorByAuditor($patient, $patient->diseaseid, $checkdates);

        // 患者用药中需要哪些检查
        $items = PatientService::getCheckItemsByPatient($patient);
        $itemstr = implode('、', $items);

        foreach ($checkdates as $ename => $checkdate) {
            if ($checkdate != '' && $checkdate <= date('Y-m-d', time() + 3600 * 24)) {
                // 发送模板消息
                $wx_uri = Config::getConfig("wx_uri");
                $url = $wx_uri . "/padrmonitor/list";

                $first = [
                    "value" => "您好，{$itemstr}检查做了么？如果还没有请尽快进行检查。如果已经检查请点击详情进行上传。",
                    "color" => ""];
                $keywords = [
                    [
                        "value" => $itemstr,
                        "color" => "#ff6600"],
                    [
                        "value" => "",
                        "color" => "#ff6600"],
                    [
                        "value" => "",
                        "color" => "#ff6600"]];
                $remark = "请点击详情进行处理，请注意您的上传内容会直接汇报给医生及医生助理。如有问题请直接与我们联系。";
                $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);
                PushMsgService::sendTplMsgToPatientByAuditor($patient, $this->myauditor, "jyjc_remind", $content, $url);

                // 置状态为 已发送
                $padrmonitor = $padrmonitors[$ename];
                if ($padrmonitor instanceof PADRMonitor) {
                    $padrmonitor->status = 1;
                }

                break;
            }
        }

        XContext::setJumpPath('/patientmedicinetargetmgr/detailofpatient?patientid=' . $patient->id);

        return self::BLANK;
    }

    // 关闭 不良反应监测
    public function doCloseAdr_monitorJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        // 关闭不良反应监测
        PADRMonitor_AutoService::closeMonitor($patient, $patient->diseaseid);

        return self::BLANK;
    }

    // 开启 用药核对
    public function doOpenMedicine_checkPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $next_check_time = XRequest::getValue('next_check_time', '0000-00-00');

        $row = [];
        $row["wxuserid"] = 0;
        $row["userid"] = 0;
        $row["patientid"] = $patient->id;
        $row["type"] = 'multiple_diseases';
        $row["plan_send_date"] = $next_check_time;
        $row["content"] = '';
        $row["status"] = 0;
        $patientmedicinecheck = PatientMedicineCheck::createByBiz($row);

        if ($next_check_time == date('Y-m-d')) {
            $patientmedicinecheck->status = 1;

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientmedicinecheck/checkofmultiplediseases?todate=" . date('Y-m-d') . "&patientmedicinecheckid=" . $patientmedicinecheck->id;

            $first = [
                "value" => "",
                "color" => ""];
            $keywords = [
                [
                    "value" => "{$patient->doctor->name}医生随访团队",
                    "color" => "#aaa"],
                [
                    "value" => "用药情况核对",
                    "color" => "#ff6600"]];
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToPatientByAuditor($patient, $this->myauditor, "followupNotice", $content, $url);
        }

        $patient->is_medicine_check = 1;

        XContext::setJumpPath('/patientmedicinetargetmgr/detailofpatient?patientid=' . $patient->id);

        return self::BLANK;
    }

    // 关闭 用药核对
    public function doCloseMedicine_checkJson () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);
        DBC::requireNotEmpty($patient, "patient is null");

        $patient->is_medicine_check = 0;

        // 关闭所有patientmedicinecheck
        $patientmedicinechecks = PatientMedicineCheckDao::getListByPatientid($patient->id, " and status in (0, 1) ");
        if (count($patientmedicinechecks) > 0) {
            foreach ($patientmedicinechecks as $patientmedicinecheck) {
                $patientmedicinecheck->status = 3;
            }
        }

        return self::BLANK;
    }
}
