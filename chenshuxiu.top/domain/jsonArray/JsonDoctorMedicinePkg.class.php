<?php

class JsonDoctorMedicinePkg
{
    // jsonArrayForIpad
    public static function jsonArrayForIpad (DoctorMedicinePkg $doctorMedicinePkg) {
        $items = $doctorMedicinePkg->getItemList();

        $arr = array();

        foreach ($items as $item) {
            $arr[] = JsonDoctorMedicinePkgItem::jsonArrayForIpad($item);
        }

        return $arr;
    }

    // jsonArrayForList
    public static function jsonArrayForList (DoctorMedicinePkg $doctorMedicinePkg) {
        $items = $doctorMedicinePkg->getItemList();

        $arr = array();

        foreach ($items as $item) {
            $arr[] = JsonDoctorMedicinePkgItem::jsonArrayForList($item);
        }

        return $arr;
    }
}