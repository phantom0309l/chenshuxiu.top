<?php

/*
 * 疾病组
 */

class DiseaseGroup extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'name'); // 疾病组名
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["name"] = $name;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "DiseaseGroup::createByBiz row cannot empty");

        $entity = DiseaseGroupDao::getByName($row["name"]);
        if ($entity instanceof DiseaseGroup) {
            return $entity;
        }

        $row["id"] = 1 + Dao::queryValue(' select max(id) as maxid from diseasegroups ');

        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDiseases() {
        return DiseaseDao::getDiseaseListByDiseasegroup($this);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function isCancer($diseasegroupid) {
        return $diseasegroupid == 3;
    }
}
