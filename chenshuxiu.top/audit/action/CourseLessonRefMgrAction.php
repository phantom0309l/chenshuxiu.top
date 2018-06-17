<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-3-21
 * Time: 下午12:10
 */

class CourseLessonRefMgrAction extends AuditBaseAction
{

    public function _construct () {
        parent::_construct();
    }

    // 课程-课文关系列表
    public function dolist () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $courseid = XRequest::getValue("courseid", 0);
        $courses = Dao::getEntityListByCond("Course", " order by id asc ");

        $sql = "select a.*
                from courselessonrefs a
                inner join lessons b on b.id = a.lessonid
                where 1=1 ";

        $cond = "";
        $bind = [];

        if ($courseid) {
            $cond .= " and a.courseid=:courseid ";
            $bind[':courseid'] = $courseid;
        }

        $cond .= " order by a.id ";
        $sql .= $cond;

        $courselessonrefs = Dao::loadEntityList4Page("CourseLessonRef", $sql, $pagesize, $pagenum, $bind);

        // 分页
        $countSql = "select count(*) as cnt
                     from courselessonrefs a
                     inner join lessons b on b.id = a.lessonid where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/courselessonrefmgr/list?courseid={$courseid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("courseid", $courseid);
        XContext::setValue("courses", $courses);
        XContext::setValue("courselessonrefs", $courselessonrefs);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 删除提交
    public function doDeletePost () {
        $courselessonrefid = XRequest::getValue('courselessonrefid', 0);
        $courselessonref = CourseLessonRef::getById($courselessonrefid);
        $courselessonref->remove();

        echo "1";
        return self::BLANK;
    }
}