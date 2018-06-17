<?php

class FbtAction extends AuditBaseAction
{

    public function doDefault () {
        return self::SUCCESS;
    }

    public function dolist () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $word = XRequest::getValue("word", '');

        $courses = CourseDao::getListByGroupstr("fbt");
        $fbtcourse = $courses[0];
        if ($word == '') {
            $courseuserrefs = CourseUserRefDao::getListByCourseOrderbyUpdatetime4Page($fbtcourse, $pagesize, $pagenum);
            $cnt = CourseUserRefDao::getCntOfCourseByUpdatetime($fbtcourse);

            $url = "/fbt/list";
            $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
            XContext::setValue("pagelink", $pagelink);
        } else {
            $sql = " SELECT cur.*
                    FROM courseuserrefs cur
                    JOIN users u on cur.userid=u.id
                    JOIN patients p on cur.patientid=p.id
                    JOIN wxusers wu ON cur.wxuserid=wu.id
                    WHERE cur.courseid = :courseid AND (
                    u.xcode like :word OR p.name LIKE :word OR wu.nickname LIKE :word )";

            $bind = [];
            $bind[':courseid'] = $fbtcourse->id;
            $bind[':word'] = "%{$word}%";

            $courseuserrefs = Dao::loadEntityList("CourseUserRef", $sql, $bind);

            $sql = " SELECT cur.*
                    FROM courseuserrefs cur
                    JOIN users u on cur.userid=u.id
                    JOIN wxusers wu ON cur.wxuserid=wu.id
                    WHERE cur.courseid = :courseid AND cur.patientid=0 AND (
                    u.xcode like :word OR wu.nickname LIKE :word )";

            $bind = [];
            $bind[':courseid'] = $fbtcourse->id;
            $bind[':word'] = "%{$word}%";

            $temprefs = Dao::loadEntityList("CourseUserRef", $sql, $bind);

            foreach ($temprefs as $a) {
                $courseuserrefs[] = $a;
            }
        }

        $yesterdayupdatecount = XAnswerSheet::getCntOfYesterdayByCourse($fbtcourse);
        XContext::setValue("courseuserrefs", $courseuserrefs);
        XContext::setValue("yesterdayupdatecount", $yesterdayupdatecount);

        // 统计方寸儿童管理服务平台和方寸课堂的用户数
        $xiaoers = CourseUserRefDao::getCntByWxshopid($fbtcourse, 1);
        $ketangs = CourseUserRefDao::getCntByWxshopid($fbtcourse, 3);

        $subscribenum = WxUserDao::getSubscribedNum(3) + $xiaoers;
        XContext::setValue("subscribenum", $subscribenum);
        XContext::setValue("xiaoers", $xiaoers);
        XContext::setValue("ketangs", $ketangs);

        return self::SUCCESS;
    }

    public function doStatistics () {
        $courses = CourseDao::getListByGroupstr("fbt");
        $fbtcourse = $courses[0];

        $values1 = array();
        $values2 = array();
        $weeks = array();

        $weekhistorys = Rpt_week_ketangDao::getList(30);

        foreach ($weekhistorys as $a) {
            $enddate = $a->enddate;
            $activecnt = $a->hwkactivecnt;
            $allnum = $a->adhd_allcnt + $a->ketang_allcnt;
            $values1[] = round(($activecnt / $allnum), 2);
            $values2[] = $a->adhd_newcnt + $a->ketang_newcnt;
            $weeks[] = $enddate;
        }

        $activityhistory = array();
        $activityhistory['week'] = json_encode(array_reverse($weeks), JSON_UNESCAPED_UNICODE);
        $activityhistory['value'] = json_encode(array_reverse($values1), JSON_UNESCAPED_UNICODE);
        XContext::setValue("activityhistory", $activityhistory);

        $addhistory = array();
        $addhistory['week'] = json_encode(array_reverse($weeks), JSON_UNESCAPED_UNICODE);
        $addhistory['value'] = json_encode(array_reverse($values2), JSON_UNESCAPED_UNICODE);
        XContext::setValue("addhistory", $addhistory);

        $patitions = CourseUserRefDao::getWeekPartitionByCourseid($fbtcourse->id);
        $weekpartition = array();

        foreach ($patitions as $date => $a) {
            $weekpartition[] = array(
                'value' => $a + 0,
                'name' => "$date");
        }

        $weekpartition = json_encode($weekpartition, JSON_UNESCAPED_UNICODE);
        XContext::setValue("weekpartition", $weekpartition);

        return self::SUCCESS;
    }

    public function dohwkhtml () {
        $courseuserrefid = XRequest::getValue("courseuserrefid", 0);
        $courseuserref = CourseUserRef::getById($courseuserrefid);

        $lessonuserrefs = LessonUserRefDao::getAllRef4Course($courseuserref->course, $courseuserref->user);

        $hwklessonuserrefs = array();
        foreach ($lessonuserrefs as $lessonuserref) {
            $hwksheet = $lessonuserref->getHwkAnswerSheet();
            if (! $hwksheet instanceof XAnswerSheet) {
                continue;
            }
            $createtime = strtotime($hwksheet->createtime);
            $date = date("Y-m-d", $createtime);
            $hwklessonuserrefs["$date"][] = $lessonuserref;
        }

        XContext::setValue("hwklessonuserrefs", $hwklessonuserrefs);
        XContext::setValue("courseuserref", $courseuserref);

        $writeHwkDays = $courseuserref->calcWriteHwkDays();
        XContext::setValue("writeHwkDays", $writeHwkDays);

        $today = date("Y-m-d");
        $studydays = XDateTime::getDaySpan($courseuserref->createtime, $today);
        XContext::setValue("studydays", $studydays);

        $comments = CommentDao::getZongjieListByUserid($courseuserref->userid);
        XContext::setValue("comments", $comments);

        return self::SUCCESS;
    }

    // 感悟列表
    public function doCommentlistofcourse () {
        $courses = CourseDao::getListByGroupstr("fbt");
        $fbtcourse = $courses[0];
        $comments = CommentDao::getListByObjtypeObjid("Course", $fbtcourse->id, 'order by id desc limit 300');

        XContext::setValue("comments", $comments);
        return self::SUCCESS;
    }

    // 感悟修改
    public function doCommentModifyHtml () {
        $commentid = XRequest::getValue("commentid", 0);
        $comment = Comment::getById($commentid);
        XContext::setValue('comment', $comment);
        return self::SUCCESS;
    }

    // 感悟修改
    public function doCommentModifyPost () {
        $commentid = XRequest::getValue("commentid", 0);
        $title = XRequest::getValue("title", '');
        $content = XRequest::getValue("content", '');

        $comment = Comment::getById($commentid);
        $comment->title = $title;
        $comment->content = $content;

        XContext::setJumpPath("/fbt/commentlistofcourse");

        return self::SUCCESS;
    }

    // 感悟删除
    public function doCommentDeletePost () {
        $commentid = XRequest::getValue("commentid", 0);

        $comment = Comment::getById($commentid);
        $comment->remove();

        XContext::setJumpPath("/fbt/commentlistofcourse");

        return self::SUCCESS;
    }

    public function doAnswer2commentofcourseJson () {
        $answerid = XRequest::getValue("answerid", 0);
        $courses = CourseDao::getListByGroupstr("fbt");
        $fbtcourse = $courses[0];

        $answer = XAnswer::getById($answerid);

        $row = array();
        $row["createtime"] = $answer->createtime;
        $row["userid"] = $answer->xanswersheet->userid;
        $row["objtype"] = 'Course';
        $row["objid"] = $fbtcourse->id;
        $row["typestr"] = 'ganwu';
        $row["title"] = $title = $answer->xanswersheet->xquestionsheet->title;
        $row["content"] = $answer->content;
        $comment = Comment::createByBiz($row);

        $str = "已添加感悟:\n\n";
        $str .= "{$title}\n\n";
        $str .= $answer->content;
        echo $str;
        return self::BLANK;
    }

    public function doRankList () {
        $shareuserarray = WxUserDao::getArrayOfRef_objidAndShareCnt();
        XContext::setValue("shareuserarray", $shareuserarray);

        $cond = " and pos >= 60 order by pos desc ";
        $courseuserrefs = Dao::getEntityListByCond("CourseUserRef", $cond, []);
        XContext::setValue("courseuserrefs", $courseuserrefs);

        return self::SUCCESS;
    }
}
