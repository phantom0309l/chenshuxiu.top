<?php

class JsonUser
{
    // jsonArray
    public static function jsonArray (User $user) {
        $arr = array();

        $arr['userid'] = $user->id;
        $arr['patientid'] = $user->patientid;
        $arr['name'] = $user->name;
        $arr['shipstr'] = $user->shipstr;
        $arr['patient_name'] = $user->patient->name;

        return $arr;
    }
}