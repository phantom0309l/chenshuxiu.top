<?php

class JsonPatientMedicinePkgItem
{
    // jsonArrayForIpad
    public static function jsonArrayForIpad (PatientMedicinePkgItem $patientMedicinePkgItem) {
        $doctorMedicineRef = $patientMedicinePkgItem->getDoctorMedicineRef();

        $arr = array();

        $arr["medicineid"] = $patientMedicinePkgItem->medicineid;
        $arr["title"] = $doctorMedicineRef->title;
        $arr["drug_dose_selectstr"] = $patientMedicinePkgItem->drug_dose;
        $arr["drug_frequency_selectstr"] = $patientMedicinePkgItem->drug_frequency;
        $arr["drug_change_selectstr"] = $patientMedicinePkgItem->drug_change;
        $arr["ischinese"] = $patientMedicinePkgItem->medicine->ischinese;
        $arr["herbs"] = Herb::edit2arr($patientMedicinePkgItem->herbjson);

        return $arr;
    }

    // jsonArrayForList
    public static function jsonArrayForList (PatientMedicinePkgItem $patientMedicinePkgItem) {
        $doctorMedicineRef = $patientMedicinePkgItem->getDoctorMedicineRef();

        $arr = array();

        $arr["medicineid"] = $patientMedicinePkgItem->medicineid;
        $arr["medicinetitle"] = $doctorMedicineRef->title;
        $arr["pos"] = $doctorMedicineRef->pos;

        $arr["drug_dose"]["arr"] = $doctorMedicineRef->getArrDrug_dose();
        $arr["drug_dose"]["selectstr"] = $patientMedicinePkgItem->drug_dose;

        $arr["drug_frequency"]["arr"] = $doctorMedicineRef->getArrDrug_frequency();
        $arr["drug_frequency"]["selectstr"] = $patientMedicinePkgItem->drug_frequency;

        $arr["drug_change"]["arr"] = $doctorMedicineRef->getArrDrug_change();
        $arr["drug_change"]["selectstr"] = '';

        $arr["doctorremark"] = $doctorMedicineRef->doctorremark;

        return $arr;
    }
}