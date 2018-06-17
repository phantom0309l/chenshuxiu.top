<?php

class JsonPrescription
{
    // jsonArrayForDwx
    public static function jsonArrayForDwx (Prescription $prescription) {
        $arr = array();
        $arr['id'] = $prescription->id;
        $arr['patientname'] = $prescription->patient_name;
        $arr['patientage'] = $prescription->patient->getAgeStr() ? $prescription->patient->getAgeStr() . "岁" : "未知";
        $arr['createtime'] = substr($prescription->createtime, 0, 10);
        $arr['doctor_is_audit'] = $prescription->doctor_is_audit;

        return $arr;
    }
}
