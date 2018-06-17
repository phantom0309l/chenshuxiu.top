<?php

class JsonRpt_patient_month_settle
{
    public static function jsonArrayForDwx (Rpt_patient_month_settle $rpt) {
        $wxuser = $rpt->patient->createuser->getMasterWxUser();
        $arr =  [
            'nickname' => $wxuser->nickname,
            'name' => $rpt->patient->name,
            'createtime' => substr($wxuser->createtime, 0, 10),
            'baodaodate' => $rpt->baodaodate,
            'month_pos' => $rpt->month_pos
        ];

        return $arr;
    }
}
