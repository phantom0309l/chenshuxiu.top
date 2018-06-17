<?php

class JsonBedTktLog
{
    // jsonArray
    public static function jsonArray (BedTktLog $bedtktlog) {
        $color_arr = array(
            'status_change' => '1996ea',
            'patient_submit' => '1996ea',
            'patient_cancel' => '36b036',
            'auditor_pass' => '36b036',
            'auditor_refuse' => 'ff7b74',
            'doctor_confirm' => '36b036',
            'patient_pass' => '1996ea',
            'patient_refuse' => '1996ea',
            'doctor_pass' => '36b036',
            'doctor_refuse' => '36b036');

        $arr = array();

        $arr['lastlog_thedate'] = $bedtktlog->createtime;
        $arr['lastlog_color'] = $color_arr[$bedtktlog->type];
        $arr['lastlog_title'] = $bedtktlog->getTypeDesc();
        $arr['lastlog_content'] = $bedtktlog->content;

        return $arr;
    }
}
