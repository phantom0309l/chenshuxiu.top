<?php

class JsonLinkman
{
    // jsonArrayAudit
    public static function jsonArray (Patient $patient, $needAll = true) {
        if ($needAll) {
            $cond = " and patientid = :patientid order by id ";
        } else {
            $cond = " and patientid = :patientid and is_master = 0 order by id ";
        }

        $bind = [
            ':patientid' => $patient->id
        ];
        $linkmans = Dao::getEntityListByCond('Linkman', $cond, $bind);

        $list = [];
        foreach ($linkmans as $linkman) {
            $list[] = [
                'id' => $linkman->id,
                'userid' => $linkman->userid,
                'patientid' => $linkman->patientid,
                'name' => $linkman->name,
                'shipstr' => $linkman->shipstr,
                'mobile' => $linkman->mobile,
                'is_master' => $linkman->is_master
            ];
        }

        return $list;
    }
}
