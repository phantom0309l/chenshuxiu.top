<?php

/*
 * PatientGroup
 * 患者治疗阶段
 */

class PatientGroup extends Entity
{

    const beitailongid = 7;


    const ppcp_vip_id = 8;
    const ppcp_high_id = 9;
    const ppcp_common_id = 10;

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'pos',  // pos
            'title',  // title
            'create_auditorid',  // 创建人
            'content'); // 描述
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'create_auditorid');
    }

    protected function init_belongtos() {
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
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "PatientGroup::createByBiz row cannot empty");

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
    public function getPatientCnt() {
        $sql = "select count(*)
            from patients
            where patientgroupid = :patientgroupid ";
        $bind = [
            ':patientgroupid' => $this->id];

        return Dao::queryValue($sql, $bind);
    }

    public static function getAllId () {
        $sql = "select id from patientgroups ";

        $ids = Dao::queryValues($sql);
        $ids[] = 0;

        return $ids;
    }
}
