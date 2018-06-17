<?php

/*
 * PatientMedicineTarget
 */
class PatientMedicineTarget extends Entity
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
            'medicineid',  // medicineid
            'drug_dose',  // 药物剂量
            'drug_frequency',  // 用药频率
            'drug_hour',  // 用药时间(用餐前后)
            'drug_change',  // 调药规则
            'auditremark',  // 运营备注
            'record_date',  // 运营添加的应用药的医嘱时间
            'createby'); // 创建人: Doctor,Auditor
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
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
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["medicineid"] = $medicineid;
    // $row["drug_dose"] = $drug_dose;
    // $row["createby"] = $createby;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientMedicineTarget::createByBiz row cannot empty");

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
        $default["drug_dose"] = '';
        $default["drug_frequency"] = '';
        $default["drug_hour"] = '';
        $default["drug_change"] = '';
        $default["createby"] = '';
        $default["auditremark"] = '';
        $default["record_date"] = date('Y-m-d');

        $row += $default;
        return new self($row);
    }

    private static function createByPatientMedicinePkgItem (PatientMedicinePkgItem $pmpitem) {

        // done pcard fix
        $pcard = PcardDao::getByPatientidDoctorid($pmpitem->patientmedicinepkg->patientid, $pmpitem->patientmedicinepkg->doctorid);

        $row = array();
        $row["wxuserid"] = $pmpitem->patientmedicinepkg->wxuserid;
        $row["userid"] = $pmpitem->patientmedicinepkg->userid;
        $row["patientid"] = $pmpitem->patientmedicinepkg->patientid;
        $row["doctorid"] = $pcard->doctorid; // done pcard fix
        $row["medicineid"] = $pmpitem->medicineid;
        $row["drug_dose"] = $pmpitem->drug_dose;
        $row["drug_frequency"] = $pmpitem->getDrug_frequencyStr();
        $row["drug_change"] = $pmpitem->drug_change;
        $row["createby"] = 'Doctor';

        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getOptionsArr () {
        $options = array(
            'drug_dose' => $this->getDrugDoseOptionsArr(),
            'drug_frequency' => $this->getDrugFrequencyOptionsArr());

        return $options;
    }

    public function getDrugDoseOptionsArr () {
        $options = array();

        $drug_dose = $this->drug_dose;
        $options[] = $drug_dose;

        $before = substr($drug_dose, 0, 1);
        $after = substr($drug_dose, 1);

        $numarr = range(1, 9);
        shuffle($numarr);

        $i = 0;
        foreach ($numarr as $num) {
            if ($i == 3) {
                break;
            }

            if ($before == $num) {
                continue;
            }

            $str = "{$num}" . $after;
            if (trim($str)) {
                $options[] = $str;
            }

            $i ++;
        }
        shuffle($options);

        return $options;
    }

    public function getDrugFrequencyOptionsArr () {
        $options = array();

        $drug_frequency = $this->drug_frequency;
        $options[] = $drug_frequency;

        $frequencyarr = Medicine::get_drug_frequency_Arr_define();
        $i = 0;
        foreach ($frequencyarr as $frequency) {
            if ($i == 3) {
                break;
            }

            if ($drug_frequency == $frequency) {
                continue;
            }

            $options[] = $frequency;

            $i ++;
        }

        shuffle($options);

        return $options;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function updateByPatientMedicinePkgItem (PatientMedicinePkgItem $pmpitem) {
        // $patient = $pmpitem->patientmedicinepkg->patient;
        // $patientmedicinetarget =
        // PatientMedicineTargetDao::getByPatientMedicine( $patient,
        // $pmpitem->medicine );

        // if( $patientmedicinetarget instanceof PatientMedicineTarget ){
        // $patientmedicinetarget->drug_dose = $pmpitem->drug_dose;
        // if( $patientmedicinetarget->createby == 'Auditor'){
        // $patientmedicinetarget->createby = 'Doctor';
        // }
        // }else{
        self::createByPatientMedicinePkgItem($pmpitem);
        // }
    }

    public static function removeAllByPatient (Patient $patient) {
        $pmts = PatientMedicineTargetDao::getListByPatient($patient);

        foreach ($pmts as $pmt) {
            $pmt->remove();
        }
    }

    //获取最新或最早的核对用药表
    public function getNewestPMSheet() {
        //核对用药的创建时间一定是大于当前应用药的生成时间；如果小于，说明最新的核对用药是对应的上一次应用药，而上一次的应用药已经被新的应用药覆盖了
        $cond = " AND patientid=:patientid AND doctorid=:doctorid AND updatetime > :updatetime ORDER BY id DESC";
        $bind = [
            ':patientid' => $this->patientid,
            ':doctorid' => $this->doctorid,
            ':updatetime' => $this->createtime,
        ];
        //患者提交的实际用药表, 取最新的那条表示患者当前实际用药的状态
        $pmSheet = Dao::getEntityByCond('PatientMedicineSheet', $cond, $bind);

        //患者没有提交
        //if (!$pmSheet) {
            //return null;
        //}
        return $pmSheet;
    }

    //获取核对用药明细
    public function getPMSheetItems() {
        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid
            AND b.medicineid=:medicineid
            ORDER BY b.drug_date DESC";
        $bind = [
            ':patientid' => $this->patientid,
            ':doctorid' => $this->doctorid,
            ':medicineid' => $this->medicineid,
        ];
        $pmsitems = Dao::loadEntityList('PatientMedicineSheetItem', $sql, $bind);
        return $pmsitems;
    }

    //最后一次用药时间
    public function getNewestDrugTime() {
        //取最新的那条pmsitem
        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid
            AND b.medicineid=:medicineid
            ORDER BY b.drug_date DESC LIMIT 1";
        $bind = [
            ':patientid' => $this->patientid,
            ':doctorid' => $this->doctorid,
            ':medicineid' => $this->medicineid,
        ];
        $pmsitem = Dao::loadEntity('PatientMedicineSheetItem', $sql, $bind);
        $drugTime = '未知';
        if ($pmsitem) {
            $drugTime = $pmsitem->getDrugDate();
        }
        return $drugTime;
    }

    //最早的服药时间
    //只能通过pmsitem去找，因为当时的核对用药pmSheet可能没有当前的这个药
    public function getOldestDrugTime() {
        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid AND b.medicineid=:medicineid
            ORDER BY b.drug_date ASC LIMIT 1";
        $bind = [
            ':patientid' => $this->patientid,
            ':doctorid' => $this->doctorid,
            ':medicineid' => $this->medicineid,
        ];

        //患者提交的实际用药表(也可能是运营手工录入的), 取最早的那条表示患者当前实际用药的状态
        $pmsitem = Dao::loadEntity('PatientMedicineSheetItem', $sql, $bind);
        $drugTime = '未知';
        if ($pmsitem) {
            $drugTime = $pmsitem->getDrugDate();
        }
        return $drugTime;
    }

    //最新的服药状态
    //0:需要确认,1:确认正确服用,2:确认错误服用, 3:停药
    public function getNewestDrugStatus() {
        $status = -1;
        $pmsitem = $this->getNewestPmsItem();
        if ($pmsitem) {
            $status = $pmsitem->status;
        }
        return $status;
    }

    //最新的用药
    public function getNewestPmsItem() {
        //取最新的那条pmsitem
        $sql = "SELECT b.* FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b ON a.id = b.patientmedicinesheetid
            WHERE a.patientid=:patientid AND a.doctorid=:doctorid AND b.medicineid=:medicineid
            AND a.updatetime > :updatetime
            ORDER BY b.drug_date desc, b.id DESC LIMIT 1";
        $bind = [
            ':patientid' => $this->patientid,
            ':doctorid' => $this->doctorid,
            ':medicineid' => $this->medicineid,
            ':updatetime' => $this->createtime,
        ];

        //患者提交的实际用药表(也可能是运营手工录入的), 取最早的那条表示患者当前实际用药的状态
        $pmsitem = Dao::loadEntity('PatientMedicineSheetItem', $sql, $bind);
        return $pmsitem;
    }

    public function getDrugStatusDesc($status) {
        $str = '';
        if ($status == -1) {
            $str = '待核对';
        } else if ($status == 0) {
            $str = '待确认';
        } else if ($status == 1) {
            $str = '正常';
        } else if ($status == 2) {
            $str = '异常';
        } else if ($status == 3) {
            $str = '停药';
        }
        return $str;
    }

    public function getCreator() {
        $str = '';
        if ($this->createby == 'Auditor') {
            $str = '运营';
        } else if ($this->createby == 'Doctor') {
            $str = '医生';
        }
        return $str;
    }

    public function getRecordDate() {
        return $this->record_date != '0000-00-00' ? $this->record_date : $this->getCreateDay();
    }
}
