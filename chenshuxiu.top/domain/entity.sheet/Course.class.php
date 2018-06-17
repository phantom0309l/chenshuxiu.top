<?php

/*
 * Course
 */
class Course extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'pictureid',  // pictureid
            'groupstr',  // 分组名称
            'papertplid',  // 量表模板id
            'tagid',  // tagid
            'title',  // 主标题
            'subtitle',  // 副标题
            'brief',  // 课程简介
            'status',  // 状态
            'title1',  // 段落标题一
            'title2',  // 段落标题二
            'title3'); // 段落标题三
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["tag"] = array(
            "type" => "Tag",
            "key" => "tagid");
        $this->_belongtos["papertpl"] = array(
            "type" => "PaperTpl",
            "key" => "papertplid");
    }

    // $row = array();
    // $row["pictureid"] = $pictureid;
    // $row["title"] = $title;
    // $row["subtitle"] = $subtitle;
    // $row["brief"] = $brief;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Course::createByBiz row cannot empty");

        $default = array();
        $default["pictureid"] = 0;
        $default["groupstr"] = '';
        $default["papertplid"] = 0;
        $default["tagid"] = 0;
        $default["title"] = '';
        $default["subtitle"] = '';
        $default["brief"] = '';
        $default["status"] = 0; // 状态
        $default["title1"] = '';
        $default["title2"] = '';
        $default["title3"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 通过courselessonrefs获取一个了课程的所有课文
    public function getLessons () {
        $refs = CourseLessonRefDao::getListByCourse($this);
        $lessons = array();
        foreach ($refs as $a) {
            $lessons[] = $a->lesson;
        }
        return $lessons;
    }

    public function getLessonids () {
        return CourseLessonRefDao::getLessonidsByCourseid($this->id);
    }

    public function getLessonCnt () {
        return CourseLessonRefDao::getCntOfCourse($this);
    }

    // 获取当前最大的pos
    public function getMaxLessonPos () {
        return CourseLessonRefDao::getMaxPosOfCourse($this);
    }

    // 获取课程关系列表
    public function getCourseUserRefs () {
        return CourseUserRefDao::getListByCourse($this);
    }

    // 学习课程的users
    public function getUsers () {
        $refs = $this->getCourseUserRefs();
        return CourseUserRef::toUserArray($refs);
    }

    public function getLessonByPos ($pos) {
        $courselessonref = CourseLessonRefDao::getByCourseidAndPos($this->id, $pos);
        return $courselessonref->lesson;
    }

    public function getUserCnt () {
        return CourseUserRefDao::getCntOfCourseByUpdatetime($this);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
