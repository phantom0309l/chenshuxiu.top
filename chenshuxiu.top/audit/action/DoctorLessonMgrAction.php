<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:03
 */
class DoctorLessonMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue("doctorid", 19);

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $lessons_content = Dao::getEntityListByCond("Lesson", " and doctorid = :doctorid and voiceid = 0 and videoid = 0 and status = 1 ", $bind);
        $lessons_voice = Dao::getEntityListByCond("Lesson", " and doctorid = :doctorid and voiceid != 0 and status = 1 ", $bind);
        $lessons_video = Dao::getEntityListByCond("Lesson", " and doctorid = :doctorid and videoid != 0 and status = 1 ", $bind);

        $course = Dao::getEntityByCond("Course", " and groupstr = 'zhuanlan' ");

        XContext::setValue("doctorid", $doctorid);
        XContext::setValue("course", $course);
        XContext::setValue("lessons_content", $lessons_content);
        XContext::setValue("lessons_voice", $lessons_voice);
        XContext::setValue("lessons_video", $lessons_video);
        return self::SUCCESS;
    }
}