<?php

/*
 * CourseLessonRefDao
 */

class CourseLessonRefDao extends Dao
{
    // 名称: getByCourseAndLesson
    // 备注:
    // 创建:
    // 修改:
    public static function getByCourseAndLesson($courseid, $lessonid) {
        $cond = " and courseid = :courseid and lessonid = :lessonid ";

        $bind = [];
        $bind[':courseid'] = $courseid;
        $bind[':lessonid'] = $lessonid;

        return Dao::getEntityByCond("CourseLessonRef", $cond, $bind);
    }

    // 名称: getByCourseAndPos
    // 备注:
    // 创建:
    // 修改:
    public static function getByCourseAndPos(Course $course, $pos) {
        return self::getByCourseidAndPos($course->id, $pos);
    }

    // 名称: getByCourseidAndPos
    // 备注:
    // 创建:
    // 修改:
    public static function getByCourseidAndPos($courseid, $pos) {
        $cond = " and courseid = :courseid and pos = :pos ";

        $bind = [];
        $bind[":courseid"] = $courseid;
        $bind[":pos"] = $pos;

        return Dao::getEntityByCond("CourseLessonRef", $cond, $bind);
    }

    // 名称: getCntOfCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfCourse(Course $course) {
        $sql = "select count(*) as cnt
            from courselessonrefs
            where courseid = :courseid ";

        $bind = array(
            ':courseid' => $course->id);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCourse(Course $course) {
        $cond = " and courseid = :courseid order by pos asc ";

        $bind = [];
        $bind[':courseid'] = $course->id;

        return Dao::getEntityListByCond("CourseLessonRef", $cond, $bind);
    }

    // 名称: getListByLesson
    // 备注:
    // 创建:
    // 修改:
    public static function getListByLesson(Lesson $lesson) {
        $cond = " and lessonid = :lessonid order by pos asc ";

        $bind = [];
        $bind[':lessonid'] = $lesson->id;

        return Dao::getEntityListByCond("CourseLessonRef", $cond, $bind);
    }

    // 名称: getListByCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCourseid($courseid, $condEx = "") {
        $cond = " and courseid = :courseid {$condEx}";

        $bind = [];
        $bind[':courseid'] = $courseid;
        return Dao::getEntityListByCond("CourseLessonRef", $cond, $bind);
    }

    // 名称: getMaxPosOfCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getMaxPosOfCourse(Course $course) {
        $sql = " select max(pos) as maxpos
            from courselessonrefs
            where courseid = :courseid";

        $bind = [];
        $bind[':courseid'] = $course->id;

        return 0 + Dao::queryValue($sql, $bind);
    }

    // 名称: getLessonidsByCourseid
    // 备注: 根据courseid 获取 lessonids
    // 创建:
    // 修改:
    public static function getLessonidsByCourseid($courseid) {
        $sql = " SELECT lessonid FROM courselessonrefs 
                 WHERE courseid=:courseid 
                 ORDER BY pos ASC ";
        $bind[':courseid'] = $courseid;

        return Dao::queryValues($sql,$bind);
    }

}
