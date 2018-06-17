<?php
/*
 * Drugitem
 */
class DrugItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'drugsheetid',  // drugsheetid
            'medicineid',  // medicineid
            'record_date',
            'type',  //
            'value',  //
            'unit',  //
            'drug_dose',  //
            'drug_frequency',  //
            'missdaycnt',  //
            'content',  // 描述
            'auditorid');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'doctorid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");

        $this->_belongtos["drugsheet"] = array(
            "type" => "DrugSheet",
            "key" => "drugsheetid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["medicineid"] = $medicineid;
    // $row["type"] = $type;
    // $row["value"] = $value;
    // $row["unit"] = $unit;
    // $row["missdaycnt"] = $missdaycnt;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Drugitem::createByBiz row cannot empty");

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["drugsheetid"] = 0;
        $default["medicineid"] = 0;
        $default["record_date"] = '0000-00-00 00:00:00';
        $default["type"] = 1;
        $default["value"] = 0;
        $default["unit"] = '';
        $default["drug_dose"] = "";
        $default["drug_frequency"] = "";
        $default["missdaycnt"] = 0;
        $default["content"] = '';
        $default["auditorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getTypeDesc () {
        return self::$typeMap[$this->type];
    }

    public function getUser () {
        return $this->patient->createuser;
    }

    public function getMedicineName () {
        return $this->medicine->name;
    }

    public function getDescStr () {
        $medicine = $this->medicine;
        return $medicine->name . "" . $this->value . "" . $medicine->unit;
    }

    public function getPatientMedicineRef () {
        return PatientMedicineRefDao::getByPatientidMedicineid($this->patientid, $this->medicineid);
    }

    public function getNextNoDrugItem () {
        return DrugItemDao::getFirstAfterDate($this->patientid, $this->record_date, 0);
    }

    public function isNoDrugItem () {
        return $this->medicineid == 0 and $this->type == 1;
    }

    public function isStopItem () {
        return $this->type == 0;
    }

    // 添加漏服信息
    public function missDays ($daynum, $content = "") {
        $this->missdaycnt = $daynum;
        $this->content = $content;
    }

    public function fillRecorderIds ($userid = 0, $wxuserid = 0) {
        $this->userid = $userid;
        $this->wxuserid = $wxuserid;
    }

    public function getDrugDose(){
        $drug_dose = "";
        $patient = $this->patient;
        $medicine = $this->medicine;
        $pcard = $patient->getMasterPcard();
        if($pcard instanceof Pcard){
            $diseaseid = $pcard->diseaseid;
            if($diseaseid==1){
                if($medicine instanceof Medicine){
                    $drug_dose = $this->value . " " . $medicine->unit;
                }else{
                    $drug_dose = $this->value;
                }
            }else{
                $drug_dose = $this->drug_dose;
                if($drug_dose==""){
                    $drug_dose = $this->value;
                }
            }
        }
        return $drug_dose;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // ---- static resourse ----
    public static $typeMap = array(
        0 => "停药",
        1 => "用药");
}
