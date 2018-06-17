<?php
// PatientMedicinePkg
// 药方:患者用药方案

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class PatientMedicinePkg extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'revisitrecordid'); // revisitrecordid

    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'revisitrecordid');
    }

    protected function init_belongtos() {
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
        $this->_belongtos["revisitrecord"] = array(
            "type" => "RevisitRecord",
            "key" => "revisitrecordid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["revisitrecordid"] = $revisitrecordid;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "PatientMedicinePkg::createByBiz row cannot empty");

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
        $default["revisitrecordid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function updatePatientMedicineTarget() {
        if ($this->patient instanceof Patient) {
            PatientMedicineTarget::removeAllByPatient($this->patient);
            $pmpitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($this->id);

            foreach ($pmpitems as $pmpitem) {
                if ($pmpitem->medicine->ischinese) {
                    continue;
                }
                PatientMedicineTarget::updateByPatientMedicinePkgItem($pmpitem);
            }
        }
    }

    // #5766 根据门诊开的药，替换应用药，停药不产生应用药
    public function replacePatientMedicineTarget() {
        if ($this->patient instanceof Patient) {
            $pmpitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($this->id);

            foreach ($pmpitems as $pmpitem) {
                if ($pmpitem->medicine->ischinese) {
                    continue;
                }
                $pmTarget = PatientMedicineTargetDao::getByPatientMedicine($this->patient, $pmpitem->medicine);
                // 停药就不需要应用药了
                if ('停药' == trim($pmpitem->drug_change)) {
                    if ($pmTarget instanceof PatientMedicineTarget) {
                        $pmTarget->remove();
                    }
                } else {
                    if ($pmTarget instanceof PatientMedicineTarget) {
                        $pmTarget->drug_dose = $pmpitem->drug_dose;
                        $pmTarget->drug_frequency = $pmpitem->getDrug_frequencyStr();
                        $pmTarget->drug_change = $pmpitem->drug_change;
                        $pmTarget->createby = 'Doctor';
                    } else {
                        PatientMedicineTarget::updateByPatientMedicinePkgItem($pmpitem);
                    }
                }
            }
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
