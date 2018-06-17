<?php

class CourseMgrAction extends AuditBaseAction
{

    public function doDefault () {
        return self::SUCCESS;
    }

    // 课程列表
    public function dolist () {
        $cond = " order by groupstr asc ";
        $courses = Dao::getEntityListByCond("Course", $cond, []);

        XContext::setValue("courses", $courses);
        return self::SUCCESS;
    }

    // 课程新建
    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $addtype = XRequest::getValue("addtype", "");
        $title = XRequest::getValue("title", "");
        $subtitle = XRequest::getValue("subtitle", "");
        $groupstr = XRequest::getValue("groupstr", "");
        $title1 = XRequest::getValue("title1", "");
        $title2 = XRequest::getValue("title2", "");
        $title3 = XRequest::getValue("title3", "");
        $pictureid = XRequest::getValue("pictureid", "0");

        // 可以富文本, 可以不安全
        $brief = XRequest::getUnSafeValue("brief", "");

        DBC::requireNotEmpty($groupstr, "课程所属分组不能为空");

        $row = array();
        $row["title"] = $title;
        $row["subtitle"] = $subtitle;
        $row["groupstr"] = $groupstr;
        $row["title1"] = $title1;
        $row["title2"] = $title2;
        $row["title3"] = $title3;
        $row["pictureid"] = $pictureid;
        $row["brief"] = $brief;

        Course::createByBiz($row);

        XContext::setJumpPath("/coursemgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $courseid = XRequest::getValue("courseid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);

        $course = Course::getById($courseid);
        DBC::requireTrue($course instanceof Course, "课程不存在:{$courseid}");
        XContext::setValue("course", $course);

        $diseasecourserefs = DiseaseCourseRefDao::getListByCourseid($courseid);
        XContext::setValue("diseasecourserefs", $diseasecourserefs);

        $search_diseasecourserefs = DiseaseCourseRefDao::getListByDiseaseidDoctorid($diseaseid, $doctorid);
        XContext::setValue("search_diseasecourserefs", $search_diseasecourserefs);

        $diseases = DiseaseDao::getListAll();
        XContext::setValue("diseases", $diseases);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $courseid = XRequest::getValue("courseid", 0);
        $title = XRequest::getValue("title", "");
        $subtitle = XRequest::getValue("subtitle", "");
        $groupstr = XRequest::getValue("groupstr", "");
        $title1 = XRequest::getValue("title1", "");
        $title2 = XRequest::getValue("title2", "");
        $title3 = XRequest::getValue("title3", "");

        // 可以富文本, 可以不安全
        $brief = XRequest::getUnSafeValue("brief", "");

        $pictureid = XRequest::getValue("pictureid", 0);

        DBC::requireNotEmpty($groupstr, "分组不能为空");

        $course = Course::getById($courseid);
        DBC::requireTrue($course instanceof Course, "课程不存在:{$courseid}");

        $course->title = $title;
        $course->brief = $brief;
        $course->subtitle = $subtitle;
        $course->groupstr = $groupstr;
        $course->title1 = $title1;
        $course->title2 = $title2;
        $course->title3 = $title3;
        $course->pictureid = $pictureid;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/coursemgr/modify?courseid=" . $courseid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doGetLessonsJson () {
        $courseid = XRequest::getValue("courseid", 0);
        $arr = array();
        $course = Course::getById($courseid);
        $courselessonrefs = CourseLessonRefDao::getListByCourse($course);
        foreach ($courselessonrefs as $a) {
            $lesson = $a->lesson;
            $temp = [];
            $temp["id"] = $lesson->id;
            $temp["title"] = $lesson->title;
            $temp["brief"] = $lesson->brief;
            $arr[] = $temp;
        }
        XContext::setValue("json", $arr);
        return self::TEXTJSON;
    }
}
