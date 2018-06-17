<?php

class JsonDoctorMedicineRef
{
    // jsonArray
    public static function jsonArray (DoctorMedicineRef $ref) {
        $arr = array();

        $arr["medicineid"] = $ref->medicineid;
        $arr["medicinetitle"] = $ref->title;
        $arr["pos"] = $ref->pos;

        $arr["drug_dose"]["arr"] = $ref->getArrDrug_dose();
        $arr["drug_dose"]["selectstr"] = $ref->getDefaultDrug_dose();

        $arr["drug_frequency"]["arr"] = $ref->getArrDrug_frequency();
        $arr["drug_frequency"]["selectstr"] = $ref->getDefaultDrug_frequency();

        $arr["drug_change"]["arr"] = $ref->getArrDrug_change();
        $arr["drug_change"]["selectstr"] = '';

        $arr["doctorremark"] = $ref->doctorremark;

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (DoctorMedicineRef $ref) {
        $arr = array();

        $arr["medicineid"] = $ref->medicineid;
        $arr["title"] = $ref->title;
        $arr["drug_dose_arr"] = $ref->getArrDrug_dose();
        $arr["drug_dose_selectstr"] = $ref->getDefaultDrug_dose();

        $arr["drug_frequency_arr"] = $ref->getArrDrug_frequency();
        $arr["drug_frequency_selectstr"] = $ref->getDefaultDrug_frequency();

        $arr["drug_change_arr"] = $ref->getArrDrug_change();
        $arr["drug_change_selectstr"] = '';

        $arr["ischinese"] = $ref->medicine->ischinese;
        $arr["herbs"] = Herb::edit2arr($ref->herbjson);

        $arr["doctorremark"] = $ref->doctorremark;

        return $arr;
    }
}