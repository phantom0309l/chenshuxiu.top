<?php

class JsonPatientRemark
{
    // jsonArray_history4Ipad
    public static function jsonArray_history4Ipad(array $patientRemarks): array
    {
        $dic = [];
        foreach ($patientRemarks as $patientRemark) {
            $dic[$patientRemark->thedate] = $dic[$patientRemark->thedate] ?? [];
            $dic[$patientRemark->thedate][] = [
                "id" => $patientRemark->id,
                "name" => $patientRemark->name,
                "content" => $patientRemark->content
            ];
        }
        $arr = [];
        foreach ($dic as $k => $v) {
            $arr[] = [
                "thedate" => $k,
                "patientremarks" => $v,
            ];
        }
//        if (!empty($patientRemarks)) {
//            $tmp_arr = [];
//            $tmp_arr["thedate"] = $patientRemarks[0]->thedate;
//            $tmp_arr["patientremarks"] = [];
//            foreach ($patientRemarks as $patientRemark) {
//                if ($tmp_arr["thedate"] != $patientRemark->thedate) {
//                    $arr[] = $tmp_arr;
//                    $tmp_arr = [];
//                    $tmp_arr["thedate"] = $patientRemark->thedate;
//                    $tmp_arr["patientremarks"] = [];
//                }
//                $tmp_arr["patientremarks"][] = [
//                    "id" => $patientRemark->id,
//                    "name" => $patientRemark->name,
//                    "content" => $patientRemark->content,
//                ];
//            }
//            $arr[] = $tmp_arr;
//        }
        return $arr;
    }
}