<?php

/*
 * Dc_patientPlan
 */
class Dc_patientPlan extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title',  // 项目名称(冗余dc_project)
            'dc_doctorprojectid',  // dc_doctorprojectid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'begin_date',  // 项目开始日期
            'end_date',  // 项目结束日期
            'papertplids',  // 量表模板ids(冗余)
            'dc_patientplan_status',  // 项目状态（0：进行中，1：已完成）
            'create_auditorid' // 创建者
);
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dc_doctorprojectid',
            'patientid',
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["dc_doctorproject"] = array(
            "type" => "Dc_doctorProject",
            "key" => "dc_doctorprojectid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["create_auditor"] = array(
            "type" => "Auditor",
            "key" => "create_auditorid");
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["dc_doctorprojectid"] = $dc_doctorprojectid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["begin_date"] = $begin_date;
    // $row["end_date"] = $end_date;
    // $row["papertplids"] = $papertplids;
    // $row["dc_patientplan_status"] = $dc_patientplan_status;
    // $row["create_auditorid"] = $create_auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dc_patientPlan::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["dc_doctorprojectid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["begin_date"] = '';
        $default["end_date"] = '';
        $default["papertplids"] = '';
        $default["dc_patientplan_status"] = 0;
        $default["create_auditorid"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDc_patientplan_statusStr () {
        // 0：进行中，1：已完成
        $row = [
            '0' => 进行中,
            '1' => 已完成];

        return $row["{$this->dc_patientplan_status}"];
    }

    public function getItemCnt () {
        $list = Dc_patientPlanItemDao::getListByDc_patientplan($this);

        return count($list);
    }
}
