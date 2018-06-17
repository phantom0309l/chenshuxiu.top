<?php
/*
 * CourselessonRef
 */
class CourseLessonRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'courseid',  // courseid
            'lessonid',  // lessonid
            'pos'); // 在一个课程中的顺序

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'courseid',
            'lessonid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
        $this->_belongtos["lesson"] = array(
            "type" => "Lesson",
            "key" => "lessonid");
    }

    // $row = array();
    // $row["courseid"] = $courseid;
    // $row["lessonid"] = $lessonid;
    // $row["pos"] = $pos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CourseLessonRef::createByBiz row cannot empty");

        $entity = CourseLessonRefDao::getByCourseAndLesson($row["courseid"], $row["lessonid"]);
        if ($entity instanceof CourseLessonRef) {
            return $entity;
        }

        $default = array();
        $default["courseid"] = 0;
        $default["lessonid"] = 0;
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
