<?php

class LessonMgrAction extends AuditBaseAction
{

    public function dolist () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $lesson_name = XRequest::getValue("lesson_name", "");

        $cond = "";
        $bind = [];

        if ($lesson_name) {
            $cond .= " and title like :lesson_name ";
            $bind[':lesson_name'] = "%{$lesson_name}%";
        }

        $cond .= " order by id ";

        $courses = Dao::getEntityListByCond("Course", " order by id asc ");
        $lessons = Dao::getEntityListByCond4Page("Lesson", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from lessons where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/lessonmgr/list?lesson_name={$lesson_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("lessons", $lessons);
        XContext::setValue("courses", $courses);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 某个课程的课文列表
    public function dolistofcourse () {
        $courseid = XRequest::getValue("courseid", 0);

        $course = Course::getById($courseid);
        DBC::requireTrue($course instanceof Course, "课程不存在:{$courseid}");
        $courselessonrefs = CourseLessonRefDao::getListByCourse($course);

        $i = 0;
        foreach ($courselessonrefs as $a) {
            $i ++;
            $a->pos = $i;
        }

        XContext::setValue("course", $course);
        XContext::setValue("courselessonrefs", $courselessonrefs);

        return self::SUCCESS;
    }

    // 新建
    public function doAdd () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $courseid = XRequest::getValue("courseid", 0);
        $course = Course::getById($courseid);

        XContext::setValue("course", $course);
        XContext::setValue("doctorid", $doctorid);
        return self::SUCCESS;
    }

    // 课文添加录音
    public function doAddVoice () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $courseid = XRequest::getValue("courseid", 0);
        $course = Course::getById($courseid);

        XContext::setValue("course", $course);
        XContext::setValue("doctorid", $doctorid);
        return self::SUCCESS;
    }

    // 课文添加视频
    public function doAddVideo () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $courseid = XRequest::getValue("courseid", 0);
        $course = Course::getById($courseid);

        XContext::setValue("course", $course);
        XContext::setValue("doctorid", $doctorid);
        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost () {
        $courseid = XRequest::getValue("courseid", "0");
        $voiceid = XRequest::getValue("voiceid", 0);
        $videoid = XRequest::getValue("videoid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $title = XRequest::getValue("title", "");
        $pictureid = XRequest::getValue("pictureid", "0");
        $keypoints = XRequest::getValue("keypoints", "");

        // 可以富文本, 可以不安全
        $brief = XRequest::getUnSafeValue("brief", "");
        $hwktip = XRequest::getUnSafeValue("hwktip", "");
        $content = XRequest::getUnSafeValue("content", "");

        $course = Course::getById($courseid);

        $row = array();
        $row["voiceid"] = $voiceid;
        $row["videoid"] = $videoid;
        $row["doctorid"] = $doctorid;
        $row["title"] = $title;
        $row["pictureid"] = $pictureid;
        $row["brief"] = $brief;
        $row["keypoints"] = $keypoints;
        $row["hwktip"] = $hwktip;
        $row["content"] = $content;

        $lesson = Lesson::createByBiz($row);

        if ($courseid > 0) {
            $row = array();
            $row["courseid"] = $courseid;
            $row["lessonid"] = $lesson->id;
            $row["pos"] = $course->getMaxLessonPos() + 1;

            CourseLessonRef::createByBiz($row);
            XContext::setJumpPath("/lessonmgr/listofcourse?courseid=" . $courseid);
        } else {
            XContext::setJumpPath("/lessonmgr/list");
        }

        return self::SUCCESS;
    }

    // 修改
    public function doModify () {
        $lessonid = XRequest::getValue("lessonid", 0);
        $lesson = Lesson::getById($lessonid);
        $courses = CourseDao::getAllCourse();
        $courselessonrefs = CourseLessonRefDao::getListByLesson($lesson);

        XContext::setValue('doctorid', $lesson->doctorid);
        XContext::setValue('doctor_name', $lesson->doctor->name);

        XContext::setValue("lesson", $lesson);
        XContext::setValue("courses", $courses);
        XContext::setValue("courselessonrefs", $courselessonrefs);

        return self::SUCCESS;
    }

    // 修改 提交
    public function doModifyPost () {
        $lessonid = XRequest::getValue("lessonid", 0);
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);
        $title = XRequest::getValue("title", "");

        // 可以富文本, 可以不安全
        $brief = XRequest::getUnSafeValue("brief", "");
        $content = XRequest::getUnSafeValue("content", "");
        $hwktip = XRequest::getUnSafeValue("hwktip", "");

        $keypoints = XRequest::getUnSafeValue("keypoints", "");
        $pictureid = XRequest::getValue("pictureid", 0);
        $open_duration = XRequest::getValue("open_duration", 0);

        $lesson = Lesson::getById($lessonid);

        $lesson->doctorid = $doctorid;
        $lesson->title = $title;
        $lesson->brief = $brief;
        $lesson->keypoints = $keypoints;
        $lesson->content = $content;
        $lesson->hwktip = $hwktip;
        $lesson->pictureid = $pictureid;
        $lesson->open_duration = $open_duration;
        XContext::setValue("lesson", $lesson);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/lessonmgr/modify?lessonid=" . $lessonid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 修改排序
    public function doPosModifyPost () {
        $courseid = XRequest::getValue('courseid', 0);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $id => $pos) {
            $courselessonref = CourseLessonRef::getById($id);
            $courselessonref->pos = $pos;
        }

        $preMsg = "已保存顺序调整,并修正序号 " . XDateTime::now();
        XContext::setJumpPath("/lessonmgr/listofcourse?courseid={$courseid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 添加所属课程
    public function doModifyAddRefPost () {
        $courseid = XRequest::getValue('courseid', 0);
        $lessonid = XRequest::getValue('lessonid', 0);
        $course = Course::getById($courseid);
        $lesson = Lesson::getById($lessonid);
        $courses = $lesson->getCourses();
        $isExist = 0;

        foreach ($courses as $a) {
            if ($courseid == $a->id) {
                $isExist = 1;
            }
        }

        if (! $isExist) {
            $row = array();
            $row["courseid"] = $courseid;
            $row["lessonid"] = $lessonid;
            $row["pos"] = $course->getMaxLessonPos() + 1;

            CourseLessonRef::createByBiz($row);
        }
        XContext::setJumpPath("/lessonmgr/modify?lessonid=" . $lessonid);

        return self::SUCCESS;
    }

    public function doModifyDelRefPost () {
        $courselessonrefid = XRequest::getValue('courselessonrefid', 0);
        $courselessonref = CourseLessonRef::getById($courselessonrefid);
        $lessonid = $courselessonref->lesson->id;
        $courselessonref->remove();

        XContext::setJumpPath("/lessonmgr/modify?lessonid=" . $lessonid);
        return self::SUCCESS;
    }

    public function doAddRefPost () {
        $openid = XRequest::getValue('openid', 0);
        $courselessonrefid = XRequest::getValue('courselessonrefid', 0);

        $wxuser = WxUserDao::getByOpenid($openid);
        $user = $wxuser->user;
        $patient = $user->patient;

        $courselessonref = CourseLessonRef::getById($courselessonrefid);

        $row = array();
        $row["wxuserid"] = $wxuser->id;
        $row["userid"] = $user->id;
        $row["patientid"] = $patient->id;
        $row["doctorid"] = 0; // 20170419 TODO by sjp : 本action的参数为啥是openid ?
        $row["courseid"] = $courselessonref->courseid;
        $row["lessonid"] = $courselessonref->lessonid;

        LessonUserRef::createByBiz($row);

        XContext::setJumpPath("/lessonmgr/listofcourse?courseid=" . $courselessonref->courseid);
        return self::SUCCESS;
    }

    public function doSetUselessJson () {
        $lessonid = XRequest::getValue('lessonid', 0);
        $lesson = Lesson::getById($lessonid);

        $lesson->status = 0;

        echo ("ok");
        return self::blank;
    }

    public function doUploadImage () {
        return self::SUCCESS;
    }
}
