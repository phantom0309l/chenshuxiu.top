<?php

class PatientEduRecordService {

    // 获取文章被阅读的总次数
    public static function getViewCntByCourseAndLesson (Course $course, Lesson $lesson) {
        $patientEduRecords = PatientEduRecordDao::getListByCourseAndLessonAndMinViewCnt($course, $lesson, 1);

        $viewCnt = 0;
        foreach ($patientEduRecords as $patientEduRecord) {
            $viewCnt += $patientEduRecord->viewcnt;
        }
        return $viewCnt;
    }



}