<?php

/*
 * PatientStage
 * 患者阶段
 */
class PatientStage extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'pos',  // pos
            'title',  // title
            'create_auditorid',  // 创建人
            'content'); // 描述
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'create_auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["create_auditor"] = array(
            "type" => "Auditor",
            "key" => "create_auditorid");
    }

    // $row = array();
    // $row["pos"] = $pos;
    // $row["title"] = $title;
    // $row["create_auditorid"] = $create_auditorid;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientStage::createByBiz row cannot empty");

        $default = array();
        $default["pos"] = 0;
        $default["title"] = '';
        $default["create_auditorid"] = 0;
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getPatientCnt () {
        $sql = "select count(*)
            from patients
            where patientstageid = :patientstageid ";
        $bind = [
            ':patientstageid' => $this->id];

        return Dao::queryValue($sql, $bind);
    }
}
