<?php
/*
 * Dg_patient
 */
class Dg_patient extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'dg_projectid'    //项目id
        ,'dg_centerid'    //中心id,冗余
        ,'doctorid'    //医生id,冗余
        ,'patientid'    //患者id
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dg_projectid' ,'dg_centerid' ,'doctorid' ,'patientid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    $this->_belongtos["dg_project"] = array ("type" => "Dg_project", "key" => "dg_projectid" );
    $this->_belongtos["dg_center"] = array ("type" => "Dg_center", "key" => "dg_centerid" );
    $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
    }

    // $row = array();
    // $row["dg_projectid"] = $dg_projectid;
    // $row["dg_centerid"] = $dg_centerid;
    // $row["doctorid"] = $doctorid;
    // $row["patientid"] = $patientid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dg_patient::createByBiz row cannot empty");

        $default = array();
        $default["dg_projectid"] =  0;
        $default["dg_centerid"] =  0;
        $default["doctorid"] =  0;
        $default["patientid"] =  0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 患者是否导入项目
    public static function isImportProject ($patientid, $dg_projectid) {
        $cond = " and dg_projectid = :dg_projectid and patientid = :patientid ";
        $bind = [];
        $bind[':dg_projectid'] = $dg_projectid;
        $bind[':patientid'] = $patientid;

        $dg_patient = Dao::getEntityByCond('Dg_patient', $cond, $bind);

        if ($dg_patient instanceof Dg_patient) {
            return 1;
        } else {
            return 2;
        }
    }

}
