<?php

/*
 * DrugSheet
 */
class DrugSheet extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  //
            'doctorid',  // doctorid
            'auditorid',  // auditorid
            'thedate',  // thedate
            'is_nodrug',  // 是否不服药,1：表示不服药
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid');
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
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["thedate"] = $thedate;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DrugSheet::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["auditorid"] = 0;
        $default["thedate"] = date("Y-m-d", time());
        $default["is_nodrug"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDrugItems () {
        return DrugItemDao::getListByDrugsheetid($this->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function createOrGetDrugSheetByWxUser (WxUser $wxuser, $thedate = "") {
        if ($thedate == "") {
            $thedate = date("Y-m-d");
        }
        $patientid = $wxuser->patientid;
        $drugsheet = DrugSheetDao::getOneByPatientidThedate($patientid, $thedate);
        if (false == $drugsheet instanceof DrugSheet) {
            $fiveIds = $wxuser->get5id();
            $row = array();
            $row["thedate"] = $thedate;
            $row += $fiveIds;
            $drugsheet = DrugSheet::createByBiz($row);
            $pipe = Pipe::createByEntity($drugsheet);
        }
        return $drugsheet;
    }

    public static function createOrGetDrugSheetByPatient (Patient $patient, $thedate = "") {
        if ($thedate == "") {
            $thedate = date("Y-m-d");
        }
        $drugsheet = DrugSheetDao::getOneByPatientidThedate($patient->id, $thedate);
        if (false == $drugsheet instanceof DrugSheet) {
            $fiveIds = $patient->get5id();
            $row = array();
            $row["thedate"] = $thedate;
            $row += $fiveIds;
            $drugsheet = DrugSheet::createByBiz($row);
            $pipe = Pipe::createByEntity($drugsheet);
        }
        return $drugsheet;
    }
}
