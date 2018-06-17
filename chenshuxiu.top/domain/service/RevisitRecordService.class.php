<?php

// RevisitRecordService
// 复诊记录服务类

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class RevisitRecordService
{

    private static function addRevisitRecord (Patient $patient, Doctor $doctor, $revisittkt, $patientmedicinepkg) {
        $row = array();
        $row['patientid'] = $patient->id;
        $row['doctorid'] = $doctor->id; // done pcard fix
        $row['thedate'] = date('Y-m-d');

        if ($revisittkt instanceof RevisitTkt) {
            $row['revisittktid'] = $revisittkt->id;
        }
        if ($patientmedicinepkg instanceof PatientMedicinePkg) {
            $row['patientmedicinepkgid'] = $patientmedicinepkg->id;
        }

        $revisitrecord = RevisitRecord::createByBiz($row);

        return $revisitrecord;
    }

    // RevisitTkt
    private static function addRevisitTkt (RevisitRecord $revisitrecord, Schedule $schedule, $checkuptplids) {
        $patient = $revisitrecord->patient;
        $doctor = $revisitrecord->doctor;
        $revisittkt_d = RevisitTktDao::getLastOfPatient_Open($patient->id, $doctor->id, 'Doctor');
        $revisittkt_p = RevisitTktDao::getLastOfPatient_Open($patient->id, $doctor->id, 'Patient');

        if ($revisittkt_d instanceof RevisitTkt) {
            $revisittkt_d->isclosed = 1;
            $revisittkt_d->closeby = 'Doctor';
        }

        if ($revisittkt_p instanceof RevisitTkt) {
            $revisittkt_p->isclosed = 1;
            $revisittkt_p->closeby = 'Doctor';
        }

        $row = array();
        $row['patientid'] = $patient->id;
        $row['doctorid'] = $doctor->id; // done pcard fix
        $row['revisitrecordid'] = $revisitrecord->id;
        $row['scheduleid'] = $schedule->id;
        $row['thedate'] = $schedule->thedate;
        $row['createby'] = "Doctor";
        $row['status'] = 1;
        $row['isclosed'] = 0;
        $row['auditstatus'] = 1; // 自动审核通过
        $row['auditorid'] = 1; // 系统审核通过

        $revisittkt = RevisitTkt::createByBiz($row);
        $revisittkt->saveCheckupTplids($checkuptplids);
        $revisitrecord->set4lock('revisittktid', $revisittkt->id);

        return $revisittkt;
    }

    private static function removeLastRevisitTkt (RevisitRecord $revisitrecord) {
        if ($revisitrecord->revisittkt instanceof RevisitTkt) {

            // 删除实体
            $revisitrecord->revisittkt->remove();

            // 删除关联任务
            OpTaskService::removeAllOpTasksByObj($revisitrecord->revisittkt);
        }

        $revisitrecord->set4lock('revisittktid', 0);
    }

    // PatientMedicinePkg
    private static function addPatientMedicinePkg (RevisitRecord $revisitrecord, $patientmedicinepkg_arr) {
        $doctor = $revisitrecord->doctor;

        $row = array();
        $row['patientid'] = $revisitrecord->patientid;
        $row['doctorid'] = $doctor->id;
        $row['revisitrecordid'] = $revisitrecord->id;

        $patientMedicinePkg = PatientMedicinePkg::createByBiz($row);

        foreach ($patientmedicinepkg_arr as $item) {
            $drug_dose = trim($item['drug_dose']);
            $drug_frequency = trim($item['drug_frequency']);
            $drug_change = trim($item['drug_change']);

            $row = array();
            $row['patientmedicinepkgid'] = $patientMedicinePkg->id;
            $row['medicineid'] = $item['medicineid'];
            $row['drug_dose'] = $drug_dose;
            $row['drug_frequency'] = $drug_frequency;
            $row['drug_change'] = $drug_change;

            $medicine = Medicine::getById($item['medicineid']);

            if ($item['herbs'] && $medicine->ischinese) {
                if (false == empty($item['herbs'])) {
                    $row["herbjson"] = Herb::arr2edit($item['herbs']);
                }
            }

            $patientMedicinePkgItem = PatientMedicinePkgItem::createByBiz($row);

            $doctormedicineref = DoctorMedicineRefDao::getByDoctoridMedicineid($doctor->id, $item['medicineid']);

            if (false == in_array($drug_dose, $doctormedicineref->getArrDrug_dose()) && $drug_dose != '') {
                $doctormedicineref->drug_dose_arr .= "|{$drug_dose}";
            }
            if (false == in_array($drug_frequency, $doctormedicineref->getArrDrug_frequency()) && $drug_frequency != '') {
                $doctormedicineref->drug_frequency_arr .= "|{$drug_frequency}";
            }
            if (false == in_array($drug_change, $doctormedicineref->getArrDrug_change()) && $drug_change != '') {
                $doctormedicineref->drug_change_arr .= "|{$drug_change}";
            }
        }

        $revisitrecord->set4lock('patientmedicinepkgid', $patientMedicinePkg->id);
        return $patientMedicinePkg;
    }

    private static function removeLastPatientMedicinePkg (RevisitRecord $revisitrecord) {
        $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($revisitrecord->patientmedicinepkgid);

        foreach ($patientmedicinepkgitems as $item) {
            $item->remove();
        }
        $revisitrecord->patientmedicinepkg->remove();
        $revisitrecord->set4lock('patientmedicinepkgid', 0);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 在action层创建revisittkt，返回一个含有revisitrecord，revisittkt的service
    // 调用点：
    // admin/action.new/RevisittktAction doAddOrModifyJson
    // dapi/action.pad/RevisitTktMgrAction doAddJson
    // ipad/action/RevisitTktAction doAddJson
    public static function createRevisitTktAtAction (Patient $patient, Doctor $doctor, Schedule $schedule, $checkuptplids) {
        $revisitrecord = RevisitRecordDao::getByPatientidDoctoridToday($patient->id, $doctor->id);

        if ($revisitrecord instanceof RevisitRecord) {
            if ($revisitrecord->revisittkt instanceof RevisitTkt) {
                self::removeLastRevisitTkt($revisitrecord);
            }
        } else {
            $revisitrecord = self::addRevisitRecord($patient, $doctor, null, null);
            Pipe::createByEntity($revisitrecord);
        }

        // 生成加号单
        $revisittkt = self::addRevisitTkt($revisitrecord, $schedule, $checkuptplids);

        // 生成流
        Pipe::createByEntity($revisittkt);

        // 生成任务: 复诊预约提醒
        OpTaskService::createOpTask_remind_RevisitTkt($revisittkt);

        return $revisitrecord;
    }

    // 在action层创建patientmedicinepkg，返回一个含有revisitrecord，patientmedicinepkg的service
    // 调用点：
    // dapi/action.pad/PatientMedicinePkgMgrAction doAddJson
    // ipad/action/PatientMedicinePkgAction doAddJson
    public static function createPatientMedicinePkgAtAction ($patient, $doctor, $patientmedicinepkg_arr) {
        $revisitrecord = RevisitRecordDao::getByPatientidToday($patient->id);

        if ($revisitrecord instanceof RevisitRecord) {
            if ($revisitrecord->patientmedicinepkg instanceof PatientMedicinePkg) {
                self::removeLastPatientMedicinePkg($revisitrecord);
            }
        } else {
            $revisitrecord = self::addRevisitRecord($patient, $doctor, null, null);
            Pipe::createByEntity($revisitrecord);
        }
        $patientMedicinePkg = self::addPatientMedicinePkg($revisitrecord, $patientmedicinepkg_arr);
        $pipe = Pipe::createByEntity($patientMedicinePkg);

        return $revisitrecord;
    }
}
