<?php

class JsonPatientRemarkTpl
{
    // jsonArray_commonWordGroup
    public static function jsonArray_commonWordGroup (PatientRemarkTpl $patientRemarkTpl) {
        $prearr = $patientRemarkTpl->getGroupArr();

        $arr = array();
        foreach ($prearr as $groupstr => $commonwordarr) {
            $temp = array();
            $temp["groupstr"] = $groupstr;
            $temp["values"] = array();
            foreach ($commonwordarr as $commonword) {
                $temp["values"][] = array(
                    "commonwordid" => $commonword->id,
                    "content" => $commonword->content);
            }
            $arr[] = $temp;
        }

        return $arr;
    }

    // jsonArray_commonWordGroup4Ipad
    public static function jsonArray_commonWordGroup4Ipad (PatientRemarkTpl $patientRemarkTpl) {
        $prearr = $patientRemarkTpl->getGroupArr();

        $arr = array();
        foreach ($prearr as $groupstr => $commonwordarr) {
            $temp = array();
            $temp["groupstr"] = $groupstr;
            $temp["words"] = array();
            foreach ($commonwordarr as $commonword) {
                $temp["words"][] = $commonword->content;
            }
            $arr[] = $temp;
        }

        return $arr;
    }

    // jsonArray_commonWordGroupForIpad 旧版 lsm
    public static function jsonArray_commonWordGroupForIpad (PatientRemarkTpl $patientRemarkTpl) {
        $prearr = $patientRemarkTpl->getGroupArr();

        $arr = array();
        foreach ($prearr as $groupstr => $commonwordarr) {
            $temp = array();
            $temp["groupstr"] = $groupstr;
            $temp["values"] = array();
            foreach ($commonwordarr as $commonword) {
                $temp["values"][] = $commonword->content;
            }
            $arr[] = $temp;
        }

        return $arr;
    }
}