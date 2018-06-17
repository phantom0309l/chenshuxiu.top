<?php
// PatientMedicinePkgItemMgrAction
class PatientMedicinePkgItemMgrAction extends AuditBaseAction
{

    public function doListHtml () {
        $patientmedicinepkgid = XRequest::getValue('patientmedicinepkgid', 0);
        $patientmedicinepkg = PatientMedicinePkg::getById($patientmedicinepkgid);
        XContext::setValue('revisitrecord', $patientmedicinepkg->revisitrecord);

        $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkgid);

        XContext::setValue('patientmedicinepkgid', $patientmedicinepkgid);
        XContext::setValue('patientmedicinepkgitems', $patientmedicinepkgitems);

        return self::SUCCESS;
    }

    // 患者用药单项目新建
    public function doAdd () {
        $patientmedicinepkgid = XRequest::getValue('patientmedicinepkgid', 0);
        $revisitrecordid = XRequest::getValue('revisitrecordid', 0);

        $patient = null;

        // 如果患者没有patientmedicinepkg，则为其创建一个
        if ($patientmedicinepkgid <= 0) {
            $revisitrecord = RevisitRecord::getById($revisitrecordid);

            $patient = $revisitrecord->patient;

            $row = array();
            $row['patientid'] = $patient->id;
            $row['doctorid'] = $patient->doctorid;
            $row['revisitrecordid'] = $revisitrecordid;

            $patientmedicinepkg = PatientMedicinePkg::createByBiz($row);
            $pipe = Pipe::createByEntity($patientmedicinepkg);

            $revisitrecord->set4lock('patientmedicinepkgid', $patientmedicinepkg->id);

        } else {
            $patientmedicinepkg = PatientMedicinePkg::getById($patientmedicinepkgid);

            $patient = $patientmedicinepkg->patient;
        }

        $medicineid = XRequest::getValue('medicineid', 0);

        // 为medicineid赋默认值
        if ($medicineid == 0) {
            $doctormedicinerefs = DoctorMedicineRefDao::getListByDoctorid($patient->doctorid);
            if (count($doctormedicinerefs) > 0) {
                $medicineid = $doctormedicinerefs[0]->medicineid;
            }
        }

        $doctormedicineref = null;
        if ($medicineid) {
            $doctormedicineref = DoctorMedicineRefDao::getByDoctoridMedicineid($patient->doctorid, $medicineid);
        }

        $notselectedmedicines = MedicineDao::getPatientNotselectedMedicines($patientmedicinepkg);

        XContext::setValue('patientmedicinepkg', $patientmedicinepkg);
        XContext::setValue('medicineid', $medicineid);
        XContext::setValue('doctormedicineref', $doctormedicineref);
        XContext::setValue('notselectedmedicines', $notselectedmedicines);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $patientmedicinepkgid = XRequest::getValue('patientmedicinepkgid', 0);
        $medicineid = XRequest::getValue('medicineid', 0);
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');

        $row = array();
        $row['patientmedicinepkgid'] = $patientmedicinepkgid;
        $row['medicineid'] = $medicineid;
        $row['drug_dose'] = $drug_dose;
        $row['drug_frequency'] = $drug_frequency;
        $row['drug_change'] = $drug_change;

        $patientmedicinepkgitem = PatientMedicinePkgItem::createByBiz($row);

        XContext::setJumpPath("/revisitrecordmgr/list?isclick=1&revisitrecordid={$patientmedicinepkgitem->patientmedicinepkg->revisitrecordid}");

        return self::SUCCESS;
    }

    // 患者用药单项目修改
    public function doModify () {
        $patientmedicinepkgitemid = XRequest::getValue('patientmedicinepkgitemid', 0);
        $patientmedicinepkgitem = PatientMedicinePkgItem::getById($patientmedicinepkgitemid);

        XContext::setValue('patientmedicinepkgitem', $patientmedicinepkgitem);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $patientmedicinepkgitemid = XRequest::getValue('patientmedicinepkgitemid', 0);
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');

        $patientmedicinepkgitem = PatientMedicinePkgItem::getById($patientmedicinepkgitemid);
        $patientmedicinepkgitem->drug_dose = $drug_dose;
        $patientmedicinepkgitem->drug_frequency = $drug_frequency;
        $patientmedicinepkgitem->drug_change = $drug_change;

        XContext::setJumpPath("/revisitrecordmgr/list?isclick=1&revisitrecordid={$patientmedicinepkgitem->patientmedicinepkg->revisitrecordid}");

        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $patientmedicinepkgitemid = XRequest::getValue('patientmedicinepkgitemid', 0);

        $patientmedicinepkgitem = PatientMedicinePkgItem::getById($patientmedicinepkgitemid);

        if ($patientmedicinepkgitem instanceof PatientMedicinePkgItem) {
            $patientmedicinepkgitem->remove();
            echo "success";
        } else {
            echo "fail";
        }

        return self::BLANK;
    }
}
