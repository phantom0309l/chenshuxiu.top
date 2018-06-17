<?php

class JsonDoctorConfig
{
    // jsonArray
    public static function jsonArray (DoctorConfig $config) {
        $arr = array();

        $arr['id'] = $config->id;
        $arr['status'] = $config->status;

        $doctorconfigtpl = $config->doctorconfigtpl;

        $arr['title'] = $doctorconfigtpl->title;
        $arr['code'] = $doctorconfigtpl->code;
        $arr['brief'] = $doctorconfigtpl->brief;

        return $arr;
    }
}