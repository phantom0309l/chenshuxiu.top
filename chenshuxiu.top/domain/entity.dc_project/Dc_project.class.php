<?php

/*
 * Dc_project
 * 药厂收集信息，项目，例如：xx药副反应收集项目等等
 */
class Dc_project extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title',  // 项目名称
            'reportor',  // 汇报人
            'report_email',  // 汇报人邮箱
            'content',  // 项目备注
            'create_auditorid' // 创建人
);
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["create_auditor"] = array(
            "type" => "Auditor",
            "key" => "create_auditorid");
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["reportor"] = $reportor;
    // $row["report_email"] = $report_email;
    // $row["content"] = $content;
    // $row["create_auditorid"] = $create_auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dc_project::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["reportor"] = '';
        $default["report_email"] = '';
        $default["content"] = '';
        $default["create_auditorid"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getCntDoctorProject () {
        $dc_doctorprojects = Dc_doctorProjectDao::getListByDc_project($this);

        return count($dc_doctorprojects);
    }
}
