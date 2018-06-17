<?php

class JsonPatientTagTpl
{
    // jsonArrayForIpad
    public static function jsonArrayForIpad (PatientTagTpl $patientTagTpl) {
        $arr = array();

        $arr["patienttagtplid"] = $patientTagTpl->id;
        $arr["pos"] = $patientTagTpl->pos;
        $arr["name"] = $patientTagTpl->name;
        $arr["content"] = $patientTagTpl->content;
        return $arr;
    }

    public static function jsonArrayForAudit (Doctor $doctor) {
        $patienttagtpls = PatientTagTplDao::getListByDoctor($doctor);
        $arr = [];
        $arr["no"] = '无标签患者';
        foreach ($patienttagtpls as $a) {
            $arr["{$a->id}"] = $a->name;
        }

        return $arr;
    }
}
