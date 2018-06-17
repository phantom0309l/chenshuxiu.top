<?php

class JsonLetter
{
    // jsonArrayForDwx
    public static function jsonArrayForDwx(Letter $letter) {
        $arr = array();
        $arr['letterid'] = $letter->id;
        $arr['patientid'] = $letter->patientid;
        $arr['patientname'] = $letter->patient->name;
        $arr['createtime'] = $letter->getCreateDay();
        $arr['content'] = $letter->content;
        $arr['audittime'] = substr($letter->audit_time, 0, 10);
        $arr['auditdatetime'] = $letter->audit_time;

        return $arr;
    }

    // jsonArrayForDwxFix
    public static function jsonArrayForDwxFix(Letter $letter) {
        $arr = array();
        $arr['letterid'] = $letter->id;
        $arr['patientname'] = $letter->patient->getMarkName();
        $arr['doctorname'] = $letter->doctor->name . '医生';
        $arr['hospitalname'] = $letter->doctor->hospital->name;
        $arr['createtime'] = $letter->getCreateDay();
        $arr['content'] = $letter->content;
        $arr['audittime'] = substr($letter->audit_time, 0, 10);
        $arr['auditdatetime'] = $letter->audit_time;

        return $arr;
    }
}
