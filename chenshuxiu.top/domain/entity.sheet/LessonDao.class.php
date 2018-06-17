<?php
/*
 * LessonDao
 */
class LessonDao extends Dao
{
    // 名称: getListByCourse
    // 备注:列表 of 课程
    // 创建:
    // 修改:
    public static function getListByCourse (Course $course) {
        $courselessonrefs = CourseLessonRefDao::getListByCourse($course);
        $lessons = array_map(function  ($x) {
            return $x->lesson;
        }, $courselessonrefs);
        return $lessons;
    }

    public static function getTreatmentLessonByDoctor (Doctor $doctor) {
        $sql = "select a.* from lessons a
        inner join courselessonrefs b on b.lessonid=a.id
        inner join courses c on c.id=b.courseid
        where c.groupstr='treatment_notice' and a.doctorid = :doctorid";

        $bind = [];
        $bind[':doctorid'] = $doctor->id;
        $lesson = Dao::loadEntity('Lesson', $sql, $bind);

        return $lesson;
    }
}
