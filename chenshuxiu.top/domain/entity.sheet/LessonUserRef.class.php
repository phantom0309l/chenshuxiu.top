<?php
/*
 * LessonUserRef
 */
class LessonUserRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'lessonid',  // lessonid
            'courseid',  // courseid
            'readtime',  // 阅读完毕时间
            'viewcnt'); // 学完以后查看了几次
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'doctorid',
            'lessonid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["lesson"] = array(
            "type" => "Lesson",
            "key" => "lessonid");
        $this->_belongtos["course"] = array(
            "type" => "Course",
            "key" => "courseid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["courseid"] = $courseid;
    // $row["lessonid"] = $lessonid;
    // $row["readtime"] = $readtime;
    // $row["viewcnt"] = $viewcnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "LessonUserRef::createByBiz row cannot empty");

        if ($row["wxuserid"] == null) {
            $row["wxuserid"] = 0;
        }

        if ($row["userid"] == null) {
            $row["userid"] = 0;
        }

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["courseid"] = 0;
        $default["lessonid"] = 0;
        $default["readtime"] = '0000-00-00 00:00:00';
        $default["viewcnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getNext () {
        $cond = " AND id > :id ORDER BY ID ASC LIMIT 1 ";
        $bind = array(
            ":id" => $this->id);
        return Dao::getEntityByCond('LessonUserRef', $cond, $bind);
    }

    public function isNotRead () {
        return $this->readtime == '0000-00-00 00:00:00';
    }

    // public function isHasTwoAnswerSheet() {
    // return $this->readtime == '0000-00-00 00:00:00';
    // }

    // 是否有巩固问卷
    public function hasTestxquestionsheet () {
        return $this->lesson->hasTestxquestionsheet();
    }

    // 是否有作业问卷
    public function hasHwkxquestionsheet () {
        return $this->lesson->hasHwkxquestionsheet();
    }

    // 是否有巩固答卷
    public function hasTestAnswerSheet () {
        $answersheet = $this->getTestAnswerSheet();
        return $answersheet instanceof XAnswerSheet;
    }

    // 是否有作业答卷
    public function hasHwkAnswerSheet () {
        $answersheet = $this->getHwkAnswerSheet();
        return $answersheet instanceof XAnswerSheet;
    }

    // 获取巩固答卷
    public function getTestAnswerSheet () {
        $xquestionsheetid = $this->lesson->testxquestionsheetid;
        if ($xquestionsheetid < 1) {
            return null;
        }
        return XAnswerSheet::getBy3params($xquestionsheetid, get_class($this), $this->id);
    }

    // 获取作业答卷
    public function getHwkAnswerSheet () {
        $xquestionsheetid = $this->lesson->hwkxquestionsheetid;
        if ($xquestionsheetid < 1) {
            return null;
        }
        return XAnswerSheet::getBy3params($xquestionsheetid, get_class($this), $this->id);
    }

    //判断巩固量表是不是都做完了
    public function hadFinishedTestAnswerSheet(){
        if( false == $this->hasTestxquestionsheet() ){
            return null;
        }
        if( false == $this->hasTestAnswerSheet() ){
            return null;
        }
        $questionsheet = $this->lesson->testxquestionsheet;
        $answersheet = $this->getTestAnswerSheet();
        $qcnt = $questionsheet->getQuestionCnt();
        $acnt = $answersheet->getAnswerCnt();
        return $qcnt == $acnt;
    }

    public function hadReadArticle(){
        return $this->readtime != "0000-00-00 00:00:00";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
