<?php
/*
 * LessonUserRefDao
 */
class LessonUserRefDao extends Dao
{
    // 名称: getAllCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getAllCnt () {
        $sql = "SELECT count(*) FROM lessonuserrefs";
        return Dao::queryValue($sql, []);
    }

    // 名称: getAllRef4Course
    // 备注:获得一个课程所有的refid
    // 创建:
    // 修改:
    public static function getAllRef4Course (Course $course, User $user) {
        $cond = " and courseid = :courseid and userid = :userid order by lessonid desc ";

        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':userid'] = $user->id;

        return Dao::getEntityListByCond("LessonUserRef", $cond, $bind);
    }

    // 名称: getByLessonUser
    // 备注:get单一ref
    // 创建:
    // 修改:
    public static function getByLessonUser (Lesson $lesson, User $user) {
        if (! ($lesson instanceof Lesson)) {
            return null;
        }

        $cond = " and userid = :userid and lessonid = :lessonid ";
        $bind = [];
        $bind[':userid'] = $user->id;
        $bind[':lessonid'] = $lesson->id;

        return Dao::getEntityByCond("LessonUserRef", $cond, $bind);
    }

    // 名称: getCntOfLesson
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfLesson ($lessonid) {
        $sql = "SELECT count(*)
            FROM lessonuserrefs
            where lessonid = :lessonid ";

        $bind = [];
        $bind[':lessonid'] = $lessonid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfPatient ($patientid) {
        $sql = "SELECT count(*) as cnt
            FROM lessonuserrefs
            where patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfUser
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfUser ($userid) {
        $sql = "SELECT count(*)
            FROM lessonuserrefs
            WHERE userid = :userid ";

        $bind = array(
            ":userid" => $userid);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByLesson4Page
    // 备注:按某一堂课分页展示
    // 创建:
    // 修改:
    public static function getListByLesson4Page ($lessonid, $pagesize, $pagenum) {

        $cond = " and lessonid = :lessonid order by createtime desc ";

        $bind = [];
        $bind[':lessonid'] = $lessonid;

        return Dao::getEntityListByCond4Page("LessonUserRef", $pagesize, $pagenum, $cond, $bind);
    }

    // 名称: getListByPatient4Page
    // 备注:按某一患者分页展示
    // 创建:
    // 修改:
    public static function getListByPatient4Page ($patientid, $pagesize = 1000, $pagenum = 1) {

        $cond = " and patientid = :patientid order by id desc ";
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond4Page("LessonUserRef", $pagesize, $pagenum, $cond, $bind);

    }

    // 名称: getListByUser4Page
    // 备注:按某一个user分页展示
    // 创建:
    // 修改:
    public static function getListByUser4Page ($userid, $pagesize, $pagenum) {

        $cond = " and userid = :userid order by id desc ";

        $bind = [];
        $bind[':userid'] = $userid;
        return Dao::getEntityListByCond4Page("LessonUserRef", $pagesize, $pagenum, $cond, $bind);
    }

    // 名称: getOneByPatientCourseLesson
    // 备注:现在用于分组相关培训课，课程文章的展示
    // 创建:txj
    // 修改:
    public static function getOneByPatientCourseLesson (Patient $patient, Course $course, Lesson $lesson) {
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":courseid"] = $course->id;
        $bind[":lessonid"] = $lesson->id;
        $cond = "and patientid = :patientid and courseid = :courseid and lessonid = :lessonid order by id desc";
        return Dao::getEntityByCond("LessonUserRef", $cond, $bind);
    }

    // 名称: getRefByUserCourseLesson
    // 备注:
    // 创建:
    // 修改:
    public static function getRefByUserCourseLesson (User $user, Course $course, Lesson $lesson) {
        $bind = [];
        $bind[":userid"] = $user->id;
        $bind[":courseid"] = $course->id;
        $bind[":lessonid"] = $lesson->id;
        $cond = "and userid = :userid and courseid = :courseid and lessonid = :lessonid ";
        $cond .= " order by id desc ";

        return Dao::getEntityByCond("LessonUserRef", $cond, $bind);
    }

}
