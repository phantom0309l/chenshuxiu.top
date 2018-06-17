<?php
// DoctorMedicinePkgItemMgrAction
class DoctorMedicinePkgItemMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);

        $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);

        $cond = "";
        $bind = [];

        if ($doctormedicinepkgid) {
            $cond .= " and doctormedicinepkgid = :doctormedicinepkgid ";
            $bind[':doctormedicinepkgid'] = $doctormedicinepkgid;
        }

        $doctormedicinepkgitems = Dao::getEntityListByCond('DoctorMedicinePkgItem', $cond, $bind);

        XContext::setValue('doctormedicinepkg', $doctormedicinepkg);
        XContext::setValue('doctormedicinepkgitems', $doctormedicinepkgitems);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doAdd () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);
        $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);
        $medicineid = XRequest::getValue('medicineid', 0);

        // 为medicineid赋默认值
        if ($medicineid == 0) {
            $doctormedicinerefs = DoctorMedicineRefDao::getListByDoctorid($doctormedicinepkg->doctorid);
            if (count($doctormedicinerefs) > 0) {
                $medicineid = $doctormedicinerefs[0]->medicineid;
            }
        }

        $doctormedicineref = null;
        if ($medicineid) {
            $doctormedicineref = DoctorMedicineRefDao::getByDoctoridMedicineid($doctormedicinepkg->doctorid, $medicineid);
        }

        $medicines = MedicineDao::getListByDoctorid($doctormedicinepkg->doctorid);
        XContext::setValue('medicines', $medicines);

        XContext::setValue('doctormedicinepkg', $doctormedicinepkg);
        XContext::setValue('medicineid', $medicineid);
        XContext::setValue('doctormedicineref', $doctormedicineref);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);
        $medicineid = XRequest::getValue('medicineid', 0);
        $drug_std_dosage = XRequest::getValue('drug_std_dosage', '');
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');
        $herbjson = XRequest::getValue("herbjson", '');

        $row = array();
        $row['doctormedicinepkgid'] = $doctormedicinepkgid;
        $row['medicineid'] = $medicineid;
        $row['drug_std_dosage'] = $drug_std_dosage;
        $row['drug_dose'] = $drug_dose;
        $row['drug_frequency'] = $drug_frequency;
        $row['drug_change'] = $drug_change;
        $row["herbjson"] = $herbjson;

        $doctormedicinepkgitem = DoctorMedicinePkgItem::createByBiz($row);

        XContext::setJumpPath("/doctormedicinepkgitemmgr/list?doctormedicinepkgid={$doctormedicinepkgid}");

        return self::SUCCESS;
    }

    public function doModify () {
        $doctormedicinepkgitemid = XRequest::getValue('doctormedicinepkgitemid', 0);
        $doctormedicinepkgitem = DoctorMedicinePkgItem::getById($doctormedicinepkgitemid);

        XContext::setValue('doctormedicinepkgitem', $doctormedicinepkgitem);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctormedicinepkgitemid = XRequest::getValue('doctormedicinepkgitemid', 0);

        $drug_std_dosage = XRequest::getValue('drug_std_dosage', '');
        $drug_dose = XRequest::getValue('drug_dose', '');
        $drug_frequency = XRequest::getValue('drug_frequency', '');
        $drug_change = XRequest::getValue('drug_change', '');
        $herbjson = XRequest::getValue("herbjson", '');

        $doctormedicinepkgitem = DoctorMedicinePkgItem::getById($doctormedicinepkgitemid);

        $doctormedicinepkgitem->drug_std_dosage = $drug_std_dosage;
        $doctormedicinepkgitem->drug_dose = $drug_dose;
        $doctormedicinepkgitem->drug_frequency = $drug_frequency;
        $doctormedicinepkgitem->drug_change = $drug_change;
        $doctormedicinepkgitem->herbjson = $herbjson;

        XContext::setJumpPath("/doctormedicinepkgitemmgr/list?doctormedicinepkgid={$doctormedicinepkgitem->doctormedicinepkgid}");

        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $doctormedicinepkgitemid = XRequest::getValue('doctormedicinepkgitemid', 0);

        $doctormedicinepkgitem = DoctorMedicinePkgItem::getById($doctormedicinepkgitemid);
        $doctormedicinepkgitem->remove();

        echo "success";

        return self::BLANK;
    }
}
