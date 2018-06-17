<?php

class JsonPcard
{
    // jsonArray 新版 by lkt
    public static function jsonArray (Pcard $pcard) {
        $arr = array();

        $arr['pcardid'] = $pcard->id;
        $arr['diseaseid'] = $pcard->diseaseid;
        $arr['disease_name'] = $pcard->disease->name;
        $arr['fee_type'] = $pcard->fee_type;
        $arr['out_case_no'] = $pcard->out_case_no;
        $arr['patientcardno'] = $pcard->patientcardno;
        $arr['patientcard_id'] = $pcard->patientcard_id;
        $arr['bingan_no'] = $pcard->bingan_no;
        $arr['complication'] = $pcard->getLastComplication();
        return $arr;
    }
}