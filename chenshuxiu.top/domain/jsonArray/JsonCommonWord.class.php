<?php

class JsonCommonWord
{
    // jsonArrayOfDoctor
    public static function jsonArrayOfDoctor (Doctor $doctor) {
        $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("Doctor", $doctor->id, "diagnosis");

        $arr = array();
        foreach ($commonwords as $commonword) {
            $arr[$commonword->groupstr][] = $commonword;
        }

        $jsonArr = array();
        foreach ($arr as $groupstr => $commonwordarr) {
            $temp = array();
            $temp["groupstr"] = $groupstr;
            $temp["words"] = array();
            foreach ($commonwordarr as $commonword) {
                $temp["words"][] = $commonword->content;
            }
            $jsonArr[] = $temp;
        }

        return $jsonArr;
    }
}