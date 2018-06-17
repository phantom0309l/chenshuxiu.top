<?php

class JsonDoctorMedicinePkgItem
{
    // jsonArrayForIpad
    public static function jsonArrayForIpad (DoctorMedicinePkgItem $doctorMedicinePkgItem) {
        $doctorMedicineRef = $doctorMedicinePkgItem->getDoctorMedicineRef();

        $arr = array();

        // item对应的DoctorMedicineRef可能被删除，暂时这样
        if (false == $doctorMedicineRef instanceof DoctorMedicineRef) {
            $arr["medicineid"] = 0;
            $arr["title"] = "";
            $arr["drug_dose_selectstr"] = "";
            $arr["drug_frequency_selectstr"] = "";
            $arr["drug_change_selectstr"] = "";
            $arr["ischinese"] = 0;
            $arr["herbs"] = array();
            $arr["doctorremark"] = "";

            return $arr;
        }

        $arr["medicineid"] = $doctorMedicinePkgItem->medicineid;
        $arr["title"] = $doctorMedicineRef->title;
        $arr["drug_dose_selectstr"] = $doctorMedicinePkgItem->drug_dose;
        $arr["drug_frequency_selectstr"] = $doctorMedicinePkgItem->drug_frequency;
        $arr["drug_change_selectstr"] = '';
        $arr["ischinese"] = $doctorMedicinePkgItem->medicine->ischinese;
        $arr["herbs"] = Herb::edit2arr($doctorMedicinePkgItem->herbjson);

        $arr["doctorremark"] = $doctorMedicineRef->doctorremark;
        return $arr;
    }

    // jsonArrayForList
    public static function jsonArrayForList (DoctorMedicinePkgItem $doctorMedicinePkgItem) {
        $doctorMedicineRef = $doctorMedicinePkgItem->getDoctorMedicineRef();

        $arr = array();

        // item对应的DoctorMedicineRef可能被删除，暂时这样
        if (false == $doctorMedicineRef instanceof DoctorMedicineRef) {
            $arr["medicineid"] = 0;
            $arr["medicinetitle"] = "";
            $arr["pos"] = 0;
            $arr["drug_dose"]["arr"] = array();
            $arr["drug_dose"]["selectstr"] = "";
            $arr["drug_frequency"]["arr"] = array();
            $arr["drug_frequency"]["selectstr"] = "";
            $arr["drug_change"]["arr"] = array();
            $arr["drug_change"]["selectstr"] = "";
            $arr["doctorremark"] = "";

            return $arr;
        }

        $arr["medicineid"] = $doctorMedicinePkgItem->medicineid;
        $arr["medicinetitle"] = $doctorMedicineRef->title;
        $arr["pos"] = $doctorMedicineRef->pos;
        $arr["drug_dose"]["arr"] = $doctorMedicineRef->getArrDrug_dose();
        $arr["drug_dose"]["selectstr"] = $doctorMedicinePkgItem->drug_dose;

        $arr["drug_frequency"]["arr"] = $doctorMedicineRef->getArrDrug_frequency();
        $arr["drug_frequency"]["selectstr"] = $doctorMedicinePkgItem->drug_frequency;
        $arr["drug_change"]["arr"] = $doctorMedicineRef->getArrDrug_change();
        $arr["drug_change"]["selectstr"] = '';

        $arr["doctorremark"] = $doctorMedicineRef->doctorremark;
        return $arr;
    }
}