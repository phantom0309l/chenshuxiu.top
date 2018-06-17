<?php
/*
 * PatientEduRecordDao
 */

class PatientEduRecordDao extends Dao
{
    public static function getListByPatientid($patientid) {
        $cond = ' AND patientid=:patientid ';
        $bind = [];
        $bind[":patientid"] = $patientid;
        return Dao::getEntityListByCond("PatientEduRecord", $cond, $bind);
    }

    public static function getListByPatientidCourseid($patientid, $courseid) {
        $cond = ' AND patientid=:patientid AND courseid=:courseid ';
        $bind = [];
        $bind[":patientid"] = $patientid;
        $bind[":courseid"] = $courseid;
        return Dao::getEntityListByCond("PatientEduRecord", $cond, $bind);
    }

    public static function getLessonidsByPatientidCourseid($patientid, $courseid) {
        $sql = "SELECT lessonid FROM patientedurecords 
                  WHERE patientid=:patientid 
                  AND courseid=:courseid ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':courseid'] = $courseid;
        return Dao::queryValues($sql, $bind);
    }

    public static function getOneByWxuseridCourseidLessonid ($wxuserid, $courseid, $lessonid) {
        $cond = ' AND wxuserid=:wxuserid AND courseid=:courseid AND lessonid=:lessonid';
        $bind = [];
        $bind[":wxuserid"] = $wxuserid;
        $bind[":courseid"] = $courseid;
        $bind[":lessonid"] = $lessonid;
        return Dao::getEntityByCond("PatientEduRecord", $cond, $bind);
    }

    public static function getListByCourseAndLesson (Course $course, Lesson $lesson) {
        $cond = ' AND courseid=:courseid AND lessonid=:lessonid ';
        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':lessonid'] = $lesson->id;

        return Dao::getEntityListByCond('PatientEduRecord', $cond, $bind);
    }

    public static function getListByCourseAndLessonAndMinViewCnt (Course $course, Lesson $lesson, $minViewcnt) {
        $cond = ' AND courseid=:courseid AND lessonid=:lessonid AND viewcnt >= :viewcnt';
        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':lessonid'] = $lesson->id;
        $bind[':viewcnt'] = $minViewcnt;

        return Dao::getEntityListByCond('PatientEduRecord', $cond, $bind);
    }

    // 获取wxuser数量
    // 可根据参数，获取指定 lessonid
    public static function getWxUserCntByCourseAndLesson (Course $course, Lesson $lesson, $isRead=false, $readTimes=0, $beforeWeek=0) {
        $sql = "SELECT COUNT(*) FROM patientedurecords WHERE courseid=:courseid AND lessonid=:lessonid ";
        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':lessonid'] = $lesson->id;

        if($isRead) {
            if($readTimes != 0) {
                $sql .= " AND viewcnt >= :readTimes ";
                $bind[':readTimes'] = $readTimes;
            }else {
                $sql .= " AND viewcnt > 0 ";
            }
        }
        if($beforeWeek != 0) {
            $startTime = date('Y-m-d', strtotime("-{$beforeWeek}week", strtotime(date('Y-m-d H:i:s'))));
            $sql .= " AND createtime >= :startTime";
            $bind[':startTime'] = $startTime;
        }

        return Dao::queryValue($sql, $bind);
    }
}