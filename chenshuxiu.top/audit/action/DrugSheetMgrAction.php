<?php

class DrugSheetMgrAction extends AuditBaseAction
{

    public function dolist () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        $drugsheets = DrugSheetDao::getListByPatientid($patientid, " order by id desc");
        $drugsheet_nearly2 = DrugSheetDao::getListByPatientid($patientid, " order by id desc limit 2");
        XContext::setValue("patient", $patient);
        XContext::setValue("drugsheets", $drugsheets);
        XContext::setValue("drugsheet_nearly2", $drugsheet_nearly2);
        return self::SUCCESS;
    }

    public function doUpdateDrugitems () {
        $drugsheetid = XRequest::getValue("drugsheetid", 0);
        $drugsheet = DrugSheet::getById($drugsheetid);
        $patient = $drugsheet->patient;

        $drugsheet_nearly2 = DrugSheetDao::getListByPatientid($patient->id, " order by id desc limit 2");

        $drug_frequency_arr = array();
        foreach(Medicine::get_drug_frequency_Arr_define() as $a){
            $drug_frequency_arr[$a] = $a;
        }

        XContext::setValue("drug_frequency_arr", $drug_frequency_arr);
        XContext::setValue("drugsheet_nearly2", $drugsheet_nearly2);
        XContext::setValue("drugsheet", $drugsheet);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doAdd () {
        $patientid = XRequest::getValue("patientid", 0);
        $patient = Patient::getById($patientid);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $patientid = XRequest::getValue("patientid", 0);
        $thedate = XRequest::getValue("thedate", "");
        $isdrug = XRequest::getValue("isdrug", 1);
        $drugsheet = DrugSheetDao::getOneByPatientidThedate($patientid, $thedate);

        $myauditor = $this->myauditor;

        if( false == $drugsheet instanceof DrugSheet ) {

            $patient = Patient::getById($patientid);
            $ids = $patient->get5id();

            $row = array();
            $row["thedate"] = $thedate;
            $row["auditorid"] = $myauditor->id;
            $row += $ids;

            if(!$isdrug){
                $row["is_nodrug"] = 1;
                $row["remark"] = "不服药";
            }
            $drugsheet = DrugSheet::createByBiz($row);
            $pipe = Pipe::createByEntity($drugsheet);

            if(!$isdrug){
                //把patientmedicineref置成不服药的状态
                $patientmedicinerefs = PatientMedicineRefDao::getListByPatient($patient);
                foreach ($patientmedicinerefs as $a) {
                    $a->status = 0;
                    $a->stopdate = date("Y-m-d");
                }
            }
        }
        XContext::setJumpPath("/drugsheetmgr/updatedrugitems?drugsheetid={$drugsheet->id}");
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $drugsheetid = XRequest::getValue("drugsheetid", 0);

        $drugsheet = DrugSheet::getById($drugsheetid);
        $drugsheet->remove();

        echo "ok";
        return self::blank;
    }
}
