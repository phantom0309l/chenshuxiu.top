<?php

/*
 * DiseaseCourseRef
 */
class DiseaseCourseRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseaseid',  // diseaseid
            'doctorid',  // doctorid
            'courseid',  // courseid
            'pos'); // 排序
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'diseaseid',
            'doctorid',
            'courseid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
    }

    // $row = array();
    // $row["diseaseid"] = $diseaseid;
    // $row["doctorid"] = $doctorid;
    // $row["courseid"] = $courseid;
    // $row["pos"] = $pos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DiseaseCourseRef::createByBiz row cannot empty");

        $default = array();
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["courseid"] = 0;
        $default["pos"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
}
