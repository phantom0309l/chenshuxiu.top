<?php

/*
 * Lesson
 */
class Lesson extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'pictureid',  // pictureid
            'voiceid',  // voiceid
            'videoid',  // videoid
            'doctorid',  // doctorid
            'testxquestionsheetid',  // 测试问卷id
            'hwkxquestionsheetid',  // 作业问卷id
            'pos',  // 在一个课程中的顺序
            'title',  // 标题
            'brief',  //
            'keypoints',  // 作业关键点
            'content',  // 课内容
            'hwktip',  // 作业提示
            'auth_level',  // 权限
            'open_duration',  // 课文开放时长
            'status',  // 状态初始化为1,无效置为0
            'voicecnt'); // 患者观看次数
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["voice"] = array(
            "type" => "Voice",
            "key" => "voiceid");
        $this->_belongtos["video"] = array(
            "type" => "Video",
            "key" => "videoid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["testxquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "testxquestionsheetid");
        $this->_belongtos["hwkxquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "hwkxquestionsheetid");
    }

    // $row = array();
    // $row["pictureid"] = $pictureid;
    // $row["pos"] = $pos;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["keypoints"] = $keypoints;
    // $row["content"] = $content;
    // $row["hwktip"] = $hwktip;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Lesson::createByBiz row cannot empty");

        $default = array();
        $default["pictureid"] = 0;
        $default["voiceid"] = 0;
        $default["videoid"] = 0;
        $default["doctorid"] = 0;
        $default["testxquestionsheetid"] = 0;
        $default["hwkxquestionsheetid"] = 0;
        $default["pos"] = 0;
        $default["title"] = '';
        $default["brief"] = '';
        $default["keypoints"] = '';
        $default["content"] = '';
        $default["hwktip"] = '';
        $default["auth_level"] = 0;
        $default["open_duration"] = 0;
        $default["status"] = 1; // 状态
        $default["voicecnt"] = 0; // 状态

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function testCallback (XQuestionSheet $sheet) {
        $this->testxquestionsheetid = $sheet->id;
    }

    public function hwkCallback (XQuestionSheet $sheet) {
        $this->hwkxquestionsheetid = $sheet->id;
    }

    // 是否有巩固问卷
    public function hasTestxquestionsheet () {
        return $this->testxquestionsheetid > 0 && $this->testxquestionsheet instanceof XQuestionSheet;
    }

    // 是否有课后作业
    public function hasHwkxquestionsheet () {
        return $this->hwkxquestionsheetid > 0 && $this->hwkxquestionsheet instanceof XQuestionSheet;
    }

    public function getContentNl2br () {
        $gh = XContext::getValue('gh');
        $content = str_replace('#gh#', $gh, $this->content);
        return nl2br($content);
    }

    // 获取学习情况
    public function getLessonUserRef (User $user) {
        return LessonUserRefDao::getByLessonUser($this, $user);
    }

    // 在课程中的位置
    public function getPosInCourse ($course) {
        $courseid = $course->id;
        $courselessonref = CourseLessonRefDao::getByCourseAndLesson($courseid, $this->id);
        return $courselessonref->pos;
    }

    public function getUserCnt () {
        return LessonUserRefDao::getCntOfLesson($this->id);
    }

    // 获取某课程中的本课的上一课
    public function getPrevInCourse ($course) {
        $previouspos = $this->getPosInCourse($course) - 1;
        if ($previouspos > 0) {
            return $course->getLessonByPos($previouspos);
        } else {
            return null;
        }
    }

    // 获取某课程中的本课的下一课
    public function getNextInCourse ($course) {
        $nextpos = $this->getPosInCourse($course) + 1;
        return $course->getLessonByPos($nextpos);
    }

    // 通过courselessonrefs获取一个了课文的所属所有课程
    public function getCourses () {
        $refs = CourseLessonRefDao::getListByLesson($this);
        $courses = array();
        foreach ($refs as $a) {
            $courses[] = $a->course;
        }
        return $courses;
    }

    // 获取顶的人数
    public function getDingCnt () {
        return LikeDao::getDingCnt(__CLASS__, $this->id);
    }

    // 获取顶的人数(含处理数据)
    public function getFixDingCnt (Course $course) {
        $pos = $this->getPosInCourse($course);

        $likecnt = 0;
        if ($pos) {
            $likecnt = number_format((1 / $pos), 2) * 100 - $pos;
            if ($likecnt < 0) {
                $likecnt = 0;
            }
        }

        $likecnt += $this->getDingCnt();
        return $likecnt;
    }

    // 获取踩的人数
    public function getCaiCnt () {
        return LikeDao::getCaiCnt(__CLASS__, $this->id);
    }

    // 获取标题（obj）
    public function getTitleStr () {
        return $this->title;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
