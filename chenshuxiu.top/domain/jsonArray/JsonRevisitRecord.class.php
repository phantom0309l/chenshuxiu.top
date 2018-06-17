<?php

class JsonRevisitRecord
{
    // jsonArrayForAdmin
    public static function jsonArrayForAdmin (RevisitRecord $revisitRecord) {
        $arr = $revisitRecord->toJsonArray();
        $arr['symptom'] = $revisitRecord->getSymptom();

        $patientmedicinepkg = $revisitRecord->patientmedicinepkg;
        if ($patientmedicinepkg instanceof PatientMedicinePkg) {
            $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkg->id);
            foreach ($patientmedicinepkgitems as $item) {
                $arr['patientmedicinepkg'][] = "{$item->medicine->name} {$item->drug_dose} {$item->getDrug_frequencyStr()} {$item->drug_change}";
            }
        }

        $revisittkt = $revisitRecord->revisittkt;
        if ($revisittkt instanceof RevisitTkt) {
            $arr['revisittkt']['thedate'] = $revisittkt->toJsonArray();
            $arr['revisittkt']['checkuptplstr_tkt'] = '';
            $checkuptpls_tkt = $revisittkt->getCheckupTpls();
            foreach ($checkuptpls_tkt as $i => $checkuptpl_tkt) {
                $arr['revisittkt']['checkuptplstr_tkt'] .= $checkuptpl_tkt->title . " ";
            }
        }

        return $arr;
    }
}