<?php

class PatientRecordHelper
{

    public static function getDiagnoseOptions (Patient $patient) {
        if ($patient->diseaseid == 22) {
            return PatientRecordILD_PH::getOptionByCode('diagnose');
        } elseif ($patient->diseaseid == 3) {
            return PatientRecordNMO::getOptionByCode('diagnose');
        } elseif ($patient->diseaseid == 6) {
            return PatientRecordMPN::getOptionByCode('diagnose');
        } else {
            return [];
        }
    }

    /**
     * 获取运营备注模板
     */
    public static function getPatientRecordTpls (Patient $patient) {
        $pcarddiseaseids = $patient->getPcardDiseaseidArr();

        $cancerdiseaseids = [8,14,15,17,19,21];
        $nmodiseaseids = [3];

        $commons = PatientRecordCommon::getPatientRecordTpls();
        $cancers = [];
        $nmos = [];

        if (false == empty(array_intersect($pcarddiseaseids, $cancerdiseaseids))) {
            $cancers = PatientRecordCancer::getPatientRecordTpls();
        }

        if (false == empty(array_intersect($pcarddiseaseids, $nmodiseaseids))) {
            $nmos = PatientRecordNMO::getPatientRecordTpls();
        }

        $listtpls = array_merge($cancers, $nmos, $commons);
        $listtpls = array_unique($listtpls);

        return $listtpls;
    }

    /**
     * 解析json格式，用于展示
     */
    public static function getShortDesc (PatientRecord $patientrecord) {
        switch ($patientrecord->code) {
            case 'cancer':
                return PatientRecordCancer::getShortDesc($patientrecord);
            case 'nmo':
                return PatientRecordNMO::getShortDesc($patientrecord);
            case 'common':
                return PatientRecordCommon::getShortDesc($patientrecord);
            default:
                return "";
        }
    }
}
