<?php
/*
 * PatientMedicineRef
 * 运营的药物备注（多疾病方向）
 */
class PatientMedicineRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'medicineid',
            'status',
            'stop_drug_type', // 0：未知 1：遵医嘱停药 2：自主停药
            'first_start_date', // 首次服药时间
            'startdate', //最近一次服用此药的开始服用时间
            'stopdate', //最近一次服用此药的停服时间
            'last_drugchange_date',
            'value',
            'unit',
            'drug_dose',
            'drug_frequency',
            'remark'
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
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
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "MedicinePatientRef::createByBiz row cannot empty");

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
        $default["medicineid"] = 0;
        $default["status"] = 0;
        $default["stop_drug_type"] = 0;
        $default["first_start_date"] = "0000-00-00";
        $default["startdate"] = "0000-00-00";
        $default["stopdate"] = '0000-00-00';
        $default["last_drugchange_date"] = '0000-00-00';
        $default["value"] = 0;
        $default["unit"] = "";
        $default["drug_dose"] = "";
        $default["drug_frequency"] = "";
        $default["remark"] = "";

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function isNotFillFirstStartDate () {
        return '0000-00-00' == $this->first_start_date;
    }

    public function typetrans () {
        return self::$typeMap[$this->status];
    }

    // ---- helper ----
    public function getReadableStatus () {
        return self::$statusMap[$this->status];
    }

    public function getDescStr () {
        $medicine = $this->medicine;
        return $medicine->name . "" . $this->value . "" . $medicine->unit;
    }

    public function getStopDrugTypeStr(){
        $arr = array(
            "0" => "未知",
            "1" => "遵医嘱停药",
            "2" => "自主停药",
        );
        return $arr[$this->stop_drug_type];
    }

    // ---- the 'set' of meta-program partion ----

    // ---- the 'get' of meta-program partion ----
    // 获取该ref的最后一条DrugItem
    public function getLastDrugItem () {
        return DrugItemDao::getLastByPatientid($this->patientid, $this->medicineid);
    }

    // 获取该ref的数据所引用的记录时间
    public function getLastRecordDate () {
        $item = $this->getLastDrugItem();
        return $item ? $item->record_date : null;
    }

    public function getDrugItems () {
        return DrugItemDao::getListByPatientidMedicineid($this->patientid, $this->medicineid);
    }

    public function getFirstValidDrugItem () {
        return DrugItemDao::getFirstValidByPatientidMedicineid($this->patientid, $this->medicineid);
    }

    // ---- read and desc ----
    public function updateStatusByValueThedate ($value, $thedate) {
        $patient = $this->patient;
        $ids = $patient->get5id();
        // 新生成的
        if ("0000-00-00" == $this->first_start_date) {
            $this->first_start_date = $thedate;
            $this->startdate = $thedate;
            $this->last_drugchange_date = $thedate;
            $this->wxuserid = $ids["wxuserid"];
            $this->userid = $ids["userid"];
            $this->doctorid = $ids["doctorid"];
            // status=0, value=0 表示停药
            if ($value == 0) {
                $this->status = 0;
            } else {
                $this->status = 1;
            }
            $this->value = $value;
        } else {
            // 之前已经有
            if (strtotime($thedate) >= strtotime($this->last_drugchange_date)) {
                // status=0, value=0 表示停药
                if ($value == 0) {
                    $this->status = 0;
                } else {
                    $this->status = 1;
                }
                $this->last_drugchange_date = $thedate;
                $this->value = $value;
            }
        }
    }

    public function getDrugDose($add_unit=true){
        $drug_dose = "";
        $patient = $this->patient;
        $medicine = $this->medicine;
        $pcard = $patient->getMasterPcard();
        if($pcard instanceof Pcard){
            $diseaseid = $pcard->diseaseid;
            if($diseaseid==1){
                if($add_unit){
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

    public static $typeMap = array(
        0 => "已停药",
        1 => "用药中");

    public static function getOrCreateByPatientidMedicineid ($patientid, $medicineid) {
        $ref = PatientMedicineRefDao::getByPatientidMedicineid($patientid, $medicineid);
        if ($ref instanceof PatientMedicineRef) {
            return $ref;
        } else {
            $row = array(
                "patientid" => $patientid,
                "medicineid" => $medicineid);

            return self::createByBiz($row);
        }
    }

    public static function getOrCreateByPatientMedicine (Patient $patient, Medicine $medicine) {
        return self::getOrCreateByPatientidMedicineid($patient->id, $medicine->id);
    }
}
