<?php
/*
 * CourseUserRefDao
 */
class CourseUserRefDao extends Dao
{
    // 名称: getByUserCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getByUserCourse (User $user, Course $course) {
        return self::getByUseridCourseid($user->id, $course->id);
    }

    // 名称: getByUseridCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getByUseridCourseid ($userid, $courseid) {
        $cond = " and userid = :userid and courseid = :courseid ";

        $bind = [];
        $bind[':userid'] = $userid;
        $bind[':courseid'] = $courseid;

        return Dao::getEntityByCond("CourseUserRef", $cond, $bind);
    }

    // 名称: getByWxuseridCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getByWxuseridCourseid ($wxuserid, $courseid) {
        $cond = " and wxuserid = :wxuserid and courseid = :courseid ";

        $bind = [];
        $bind[':wxuserid'] = $wxuserid;
        $bind[':courseid'] = $courseid;

        return Dao::getEntityByCond("CourseUserRef", $cond, $bind);
    }

    // 名称: getCntByWxshopid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByWxshopid (Course $course, $wxshopid) {
        $ids = UserDao::getTestUseridsStr();
        $sql = "select count(*) as cnt
            from courseuserrefs a
            inner join wxusers b on a.wxuserid = b.id
            where courseid = :courseid and b.wxshopid = :wxshopid and a.userid not in ({$ids})";

        $bind = [];
        $bind[':courseid'] = $course->id;
        $bind[':wxshopid'] = $wxshopid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfCourseByReallyStart
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfCourseByReallyStart (Course $course) {
        $sql = " select count(*)
            from courseuserrefs
            where courseid = :courseid and pos <> 0";
        $bind = [];
        $bind[':courseid'] = $course->id;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfCourseByUpdatetime
    // 备注:按更新时间排序 分页所需要的计数
    // 创建:
    // 修改:
    public static function getCntOfCourseByUpdatetime (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = " select count(*)
            from courseuserrefs
            where courseid = :courseid and userid not in ({$ids}) ";
        $bind = [];
        $bind[':courseid'] = $course->id;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getFinishCntInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getFinishCntInOneCourse (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = " select count(*)
            from courseuserrefs
            where courseid = :courseid AND pos=70 AND userid NOT IN ({$ids}) ";
        $bind = array(
            ":courseid" => $course->id);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList () {
        $cond = " ORDER BY id ASC ";
        return Dao::getEntityListByCond("CourseUserRef", $cond);
    }

    // 名称: getListByCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getListByCourse (Course $course) {
        $cond = " and courseid = :courseid order by pos asc ";
        $bind = [];
        $bind[':courseid'] = $course->id;
        return Dao::getEntityListByCond("CourseUserRef", $cond, $bind);
    }

    // 名称: getListByCourseOrderbyUpdatetime
    // 备注:按更新时间排序
    // 创建:
    // 修改:
    public static function getListByCourseOrderbyUpdatetime (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $cond = " and courseid = :courseid
            and userid not in ({$ids})
            order by updatetime desc ";
        $bind = [];
        $bind[':courseid'] = $course->id;

        return Dao::getEntityListByCond("CourseUserRef", $cond, $bind);
    }

    // 名称: getListByCourseOrderbyUpdatetime4Page
    // 备注:
    // 创建:
    // 修改:
    // 按更新时间排序 4page
    public static function getListByCourseOrderbyUpdatetime4Page (Course $course, $pagesize, $pagenum) {
        $ids = UserDao::getTestUseridsstr();

        $cond = " and courseid = :courseid
            and userid not in ({$ids})
            order by updatetime desc ";
        $bind = [];
        $bind[':courseid'] = $course->id;

        return Dao::getEntityListByCond4Page("CourseUserRef", $pagesize, $pagenum, $cond, $bind);
    }

    // 名称: getListByUser
    // 备注:
    // 创建:
    // 修改:
    public static function getListByUser (User $user, $groupstr = false) {
        $sql = "select cur.*
            from courseuserrefs cur ";
        if ($groupstr) {
            $sql .= " INNER JOIN courses c on c.id = cur.courseid and c.groupstr = '{$groupstr}' ";
        }
        $sql .= " where cur.userid = :userid order by cur.pos asc";

        $bind = [];
        $bind[':userid'] = $user->id;

        return Dao::loadEntityList("CourseUserRef", $sql, $bind);
    }

    // 名称: getListRankHardWorkInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getListRankHardWorkInOneCourse (Course $course) {
        $today = date("Y-m-d");
        $ids = UserDao::getTestUseridsstr();

        $cond = " and pos > 1 and  courseid = :courseid AND userid NOT IN ({$ids})
            order by ((TIMESTAMPDIFF(DAY,begintime,:today) + 1) / pos) ASC
            limit 20 ";

        $bind = array(
            ":today" => $today,
            ":courseid" => $course->id);

        return Dao::getEntityListByCond("CourseUserRef", $cond, $bind);
    }

    // // 某user是否加入某course
    // public static function isJoinThisCourse(User $user, Course $course) {
    // $cond = " and userid=:userid and courseid=:courseid ";
    // $bind = array ();
    // $bind [':userid'] = $user->id;
    // $bind [':courseid'] = $course->id;
    // $tmpobj = Dao::getEntityByCond ( "CourseUserRef", $cond, $bind );
    //
    // if ($tmpobj instanceof CourseUserRef) {
    // return true;
    // } else {
    // return false;
    // }
    // }

    // 名称: getListRankPosInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getListRankPosInOneCourse (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = " select *
            from ( select *
                from courseuserrefs
                where pos < 70 and courseid = :courseid AND userid NOT IN ({$ids})
                order by begintime desc ) tt
            group by tt.pos
            order by tt.pos desc
            limit 10 ";

        $bind = array(
            ":courseid" => $course->id);

        return Dao::loadEntityList("CourseUserRef", $sql, $bind);
    }

    // 名称: getNewWeeklyAddByCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getNewWeeklyAddByCourseid ($courseid) {
        $ids = UserDao::getTestUseridsstr();
        $course = Course::getById($courseid);
        $lesson = $course->getLessonByPos(1);
        $hwkxquestionsheetid = $lesson->hwkxquestionsheetid;

        $today = date("Y-m-d");
        $fbtbegin_firstmonday = "2015-08-17"; // 有培训课后的第一个星期一

        $interval = XDateTime::getDaySpan($fbtbegin_firstmonday, $today);
        $weeks = array();

        for ($i = 7; $i < $interval + 7; $i += 7) {
            $tmpfrom = XDateTime::getNewDate($fbtbegin_firstmonday, $i - 7);
            $tmpto = XDateTime::getNewDate($fbtbegin_firstmonday, $i);

            $sql = "SELECT count(*)
                FROM xanswersheets
                WHERE userid not in ({$ids}) and xquestionsheetid = :hwkxquestionsheetid
                and date(createtime) BETWEEN :tmpfrom and :tmpto";
            $bind = array(
                ":tmpfrom" => "$tmpfrom",
                ":tmpto" => "$tmpto",
                ":hwkxquestionsheetid" => $hwkxquestionsheetid);

            $weeks["$tmpfrom"] = Dao::queryValue($sql, $bind);
        }
        return $weeks;
    }

    // 名称: getUserHardWorkInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getUserHardWorkInOneCourse (User $user, Course $course) {
        $courseuserref = self::getByUserCourse($user, $course);
        $today = date("Y-m-d");
        return XDateTime::getDaySpan($courseuserref->begintime, $today) / $courseuserref->pos;
    }

    // 名称: getUserRankOfHardWorkInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getUserRankOfHardWorkInOneCourse (User $user, Course $course) {
        $courseuserref = self::getByUserCourse($user, $course);
        $today = date("Y-m-d");
        $ids = UserDao::getTestUseridsstr();

        $activity = XDateTime::getDaySpan($courseuserref->createtime, $today) / $courseuserref->pos;

        $sql = " select count(*)
            from courseuserrefs
            where pos > 1 and (TIMESTAMPDIFF(DAY,begintime,:today)/pos) < :activity
                and courseid = :courseid AND userid NOT IN ({$ids})";

        $bind = array(
            ":today" => $today,
            ":activity" => $activity,
            ":courseid" => $course->id);

        return Dao::queryValue($sql, $bind) + 1;
    }

    // 名称: getUserRankOfPosInOneCourse
    // 备注:
    // 创建:
    // 修改:
    public static function getUserRankOfPosInOneCourse (User $user, Course $course) {
        $courseuserref = self::getByUserCourse($user, $course);
        $ids = UserDao::getTestUseridsstr();

        $sql = " select count(*)
            from courseuserrefs
            where pos > :pos and courseid = :courseid AND userid NOT IN ({$ids}) ";
        $bind = array(
            ":pos" => $courseuserref->pos,
            ":courseid" => $course->id);

        return Dao::queryValue($sql, $bind) + 1;
    }

    // 名称: getWeekActivityHistoryByCourseid
    // 备注:周活跃率历史统计
    // 创建:
    // 修改:
    public static function getWeekActivityHistoryByCourseid ($courseid) {
        $ids = UserDao::getTestUseridsstr();

        $today = date("Y-m-d");
        $fbtbegin_firstmonday = "2015-08-17"; // 有培训课后的第一个星期一

        $interval = XDateTime::getDaySpan($fbtbegin_firstmonday, $today);
        $weeks = array();

        for ($i = 6; $i < $interval + 7; $i += 7) {
            $tmpfrom = XDateTime::getNewDate($fbtbegin_firstmonday, $i - 6);
            $tmpto = XDateTime::getNewDate($fbtbegin_firstmonday, $i);
            $bind = array(
                ":tmpfrom" => "$tmpfrom",
                ":tmpto" => "$tmpto",
                ":courseid" => $courseid);

            $sql = "select count(distinct(xas.userid))
                from xanswersheets xas
                join lessons ls on ls.hwkxquestionsheetid = xas.xquestionsheetid
                join lessonuserrefs lus on lus.lessonid = ls.id
                where xas.userid not in ({$ids}) and lus.courseid = :courseid
                and date(xas.createtime) BETWEEN :tmpfrom and :tmpto";
            $weeks["$tmpfrom"] = Dao::queryValue($sql, $bind);
        }
        $saturdays = array();

        for ($i = 0; $i < $interval + 7; $i += 7) {
            $tmpsaturday = XDateTime::getNewDate($fbtbegin_firstmonday, $i + 5);
            $tmpmonday = XDateTime::getNewDate($fbtbegin_firstmonday, $i);

            $sql = "select count(*)
                from courseuserrefs
                where userid not in ({$ids})
                and to_days(createtime) <= to_days(:tmpsaturday)
                and courseid = :courseid";
            $bind = array(
                ":tmpsaturday" => "$tmpsaturday",
                ":courseid" => $courseid);

            $saturdays["$tmpmonday"] = Dao::queryValue($sql, $bind);
        }

        $weekhistorys = array();
        foreach ($weeks as $date => $a) {
            $weekhistorys["$date"] = sprintf("%0.2f", $a / $saturdays[$date]);
        }
        return $weekhistorys;
    }

    // 名称: getWeekPartitionByCourseid
    // 备注:
    // 创建:
    // 修改:
    public static function getWeekPartitionByCourseid ($courseid) {
        $sql = " select count(*)
            from courselessonrefs
            where courseid = :courseid ";
        $bind = array(
            ":courseid" => $courseid);

        $weekcnt = Dao::queryValue($sql, $bind) / 7;

        $patitions = array();
        for ($i = 1; $i <= $weekcnt; $i ++) {
            $sql = " select count(*)
                from courseuserrefs
                where courseid = :courseid and pos >= :posfrom and pos <= :posto ";
            $bind = array(
                ":courseid" => $courseid,
                ":posfrom" => ($i - 1) * 7 + 1,
                ":posto" => $i * 7);

            $patitions["$i"] = Dao::queryValue($sql, $bind);
        }

        return $patitions;
    }

}
