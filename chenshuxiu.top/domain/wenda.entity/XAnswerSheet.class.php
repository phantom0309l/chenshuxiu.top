<?php

/*
 * XAnswerSheet
 */
class XAnswerSheet extends Entity
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
            'xquestionsheetid',  // 问卷id
            'objtype',  // 关联对象type,不是问题上那个objtype
            'objid',  // 关联对象id,不是问题上那个objid
            'score'); // 得分
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'xquestionsheetid',
            'objtype',
            'objid');
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

        $this->_belongtos["xquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "xquestionsheetid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    public function getNext () {
        $cond = " AND id > :id ORDER BY ID ASC LIMIT 1 ";

        $bind = array(
            ":id" => $this->id);

        return Dao::getEntityByCond('XAnswerSheet', $cond, $bind);
    }

    // 获取成绩数组
    public function getResultStr () {
        return ($this->getRightCnt() >= $this->getErrorCnt()) ? '今日成绩很不错哟' : '今日成绩不太好';
    }

    // 成功数目
    public function getRightCnt () {
        return XAnswer::getRightCntOfXAnswerSheet($this);
    }

    // 错误数目
    public function getErrorCnt () {
        return XAnswer::getErrorCntOfXAnswerSheet($this);
    }

    // 条目列表
    public function getItemList () {
        return $this->getAnswers();
    }

    // 答案
    private $answers = null;
    // getAnswers
    public function getAnswers ($issimple = 0) {
        if ($this->answers === null) {
            $this->answers = XAnswer::getArrayOfXAnswerSheet($this);
        }

        if (0 == $issimple) {
            return $this->answers;
        }

        $answers = array();
        foreach ($this->answers as $a) {
            if ($a->xquestion->issimple) {
                $answers[] = $a;
            }
        }

        return $answers;
    }

    public function getLastAnswer () {
        $answers = $this->getAnswers();

        $answer = array_pop($answers);
        return $answer;
    }

    // getAnswer
    public function getAnswer ($xquestionid) {
        return XAnswer::getByXQuestionIdOfXAnswerSheet($this, $xquestionid);
    }

    // 获取SNAP-IV评估较严重情况答案数
    public function getADHDIVCntOfSerious ($glt, $baseNum) {
        return XAnswer::getADHDIVCntOfSerious($this, $glt, $baseNum);
    }

    // 已作答案数目
    public function getAnswerCnt () {
        return XAnswer::getCntOfXAnswerSheet($this);
    }

    // 问题数目
    public function getQuestionCnt () {
        return $this->xquestionsheet->getQuestionCnt();
    }

    // 问题数目
    public function getMaxQuestionPos () {
        return $this->xquestionsheet->getMaxQuestionPos();
    }

    // 获取下一题 Html
    public function getNextQuestionHtml ($prepos) {
        $entity = $this->getNextAnswerOrQuestion($prepos);
        if ($entity instanceof XAnswer) {
            return $entity->getHtml();
        }

        if ($entity instanceof XQuestion) {
            return $entity->getHtml($this);
        }

        return '';
    }

    // 获取下一题 QuestionCtr
    public function getNextQuestionCtr ($prepos) {
        $entity = $this->getNextAnswerOrQuestion($prepos);
        if ($entity instanceof XAnswer) {
            return $entity->getQuestionCtr();
        }

        if ($entity instanceof XQuestion) {
            return $entity->getQuestionCtr($this);
        }

        return null;
    }

    // 获取下一题
    public function getNextAnswerOrQuestion ($prepos = 0) {
        $xanswers = $this->getAnswers();

        foreach ($xanswers as $a) {
            if ($a->pos > $prepos) {
                return $a;
            }
        }

        $xquestions = $this->xquestionsheet->getQuestions();

        foreach ($xquestions as $q) {
            if ($q->pos > $prepos) {
                return $q;
            }
        }

        return null;
    }

    public function getAnswerByEname ($ename) {
        $xquestion = XQuestion::getByXQuestionSheetEname($this->xquestionsheet, $ename);
        return $this->getAnswer($xquestion->id);
    }

    public function getCnt ($condFix = "") {
        $sql = "SELECT COUNT(*)
            FROM xanswersheets
            WHERE xquestionsheetid=:xquestionsheetid
            AND patientid=:patientid {$condFix} ";

        $bind = array(
            ":xquestionsheetid" => $this->xquestionsheetid,
            ":patientid" => $this->patientid);

        return Dao::queryValue($sql, $bind);
    }

    // 复制一个答卷
    public function copyOne ($objNew) {
        $row = array();
        $row['wxuserid'] = $this->wxuserid;
        $row['userid'] = $this->userid;
        $row['patientid'] = $this->patientid;
        $row['doctorid'] = $this->doctorid;
        $row['xquestionsheetid'] = $this->xquestionsheetid;
        $row['objtype'] = get_class($objNew);
        $row['objid'] = $objNew->id;
        $row['score'] = $this->score;

        $xanswersheetnew = self::createByBiz($row);
        // 修改量表答卷id
        $objNew->PaperCallback($xanswersheetnew);

        // 复制答案
        $answers = $this->getAnswers();
        foreach ($answers as $answer) {
            $answer->copyOne($xanswersheetnew);
        }
    }
    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["xquestionsheetid"] = $xquestionsheetid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XAnswerSheet::createByBiz row cannot empty");

        if ($row["xquestionsheetid"] && $row['objtype'] && $row['objid']) {
            $xquestionsheet = XQuestionSheet::getById($row["xquestionsheetid"]);
            if (false == $xquestionsheet->isOfGantong()) {
                $entity = self::getBy3params($row["xquestionsheetid"], $row['objtype'], $row['objid']);
                if ($entity instanceof XAnswerSheet) {
                    return $entity;
                }
            }
        }

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
        $default["xquestionsheetid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["score"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////
    // getBy2params 关联对象
    public static function getBy3params ($xquestionsheetid, $objtype, $objid) {
        $cond = "AND xquestionsheetid=:xquestionsheetid AND objtype=:objtype AND objid=:objid order by id desc";

        $bind = [];
        $bind[':xquestionsheetid'] = $xquestionsheetid;
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityByCond('XAnswerSheet', $cond, $bind);
    }

    // getOfObj : obj + objcode
    public static function getByObj ($xquestionsheetid, EntityBase $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;
        return self::getBy3params($xquestionsheetid, $objtype, $objid);
    }

    // 答卷列表 of 问卷
    public static function getArrayOfXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $cond = "AND xquestionsheetid=:xquestionsheetid";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id);

        return Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
    }

    // getListByPatient
    public static function getListByPatient ($patientid) {
        $cond = "";
        $bind = [];

        $cond .= " AND patientid = :patientid order by id ";
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
    }

    public static function getListByPatientidObjtypeObjid ($patientid, $objtype, $objid) {
        $cond = "";
        $bind = [];

        $cond .= " AND patientid = :patientid AND objtype = :objtype AND objid = :objid order by id ";
        $bind[':patientid'] = $patientid;
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        return Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
    }

    // getListByPatient
    public static function getListByPatientidXquestionsheetid ($patientid, $xquestionsheetid) {
        $cond = "";
        $bind = [];

        $cond .= " AND patientid = :patientid AND xquestionsheetid = :xquestionsheetid order by id ";
        $bind[':patientid'] = $patientid;
        $bind[':xquestionsheetid'] = $xquestionsheetid;

        return Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
    }

    public static function getFirst () {
        $cond = " ORDER BY ID ASC LIMIT 1 ";
        return Dao::getEntityByCond('XAnswerSheet', $cond, []);
    }

    // 答卷数 of 问题
    public static function getCntByXQuestionEnameAndPatientid ($ename, $patientid) {
        $sql = "SELECT count(a.id) as cnt FROM xanswersheets a
            INNER JOIN xanswers b ON b.xanswersheetid=a.id
            INNER JOIN xquestions c ON c.id=b.xquestionid
            WHERE c.ename = :ename AND a.patientid = :patientid";

        $bind = [];
        $bind[':ename'] = $ename;
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 问题数 of 问卷
    public static function getCntOfXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $sql = "select count(*) as cnt
                from xanswersheets
                where xquestionsheetid=:xquestionsheetid ";

        $bind = [];
        $bind[':xquestionsheetid'] = $xquestionsheet->id;
        return Dao::queryValue($sql, $bind);
    }

    public static function getCntOfTodayByCourse (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = "SELECT COUNT(*) FROM(
                    SELECT COUNT(*) FROM xanswersheets xas
                    JOIN lessonuserrefs lur ON xas.objid=lur.id
                    WHERE lur.courseid=:courseid AND xas.createtime >= :today
                    AND xas.userid NOT IN ({$ids})
                    GROUP BY xas.userid) tt
                ";

        $bind = array(
            ":courseid" => $course->id,
            ":today" => date("Y-m-d"));

        return Dao::queryValue($sql, $bind);
    }

    public static function getCntOfYesterdayByCourse (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = "SELECT COUNT(*) FROM(
                    SELECT COUNT(*) FROM xanswersheets xas
                    JOIN lessonuserrefs lur ON xas.objid=lur.id
                    WHERE lur.courseid=:courseid AND xas.createtime >= :yesterday
                    AND xas.createtime < :today
                    AND xas.userid NOT IN ({$ids})
                    GROUP BY xas.userid)tt
                ";

        $bind = array(
            ":courseid" => $course->id,
            ":today" => date("Y-m-d"),
            ":yesterday" => XDateTime::getNewDate(date("Y-m-d"), - 1));

        return Dao::queryValue($sql, $bind);
    }

    public static function getCntOfPassWelcome (Course $course) {
        $ids = UserDao::getTestUseridsstr();

        $sql = "SELECT COUNT(*) FROM(
                    SELECT COUNT(*) FROM xanswersheets xas
                    JOIN lessonuserrefs lur ON xas.objid=lur.id
                    JOIN pipes p ON lur.userid=p.userid
                    WHERE lur.courseid=:courseid AND p.objcode='FbtPass_Can'
                    AND xas.userid NOT IN ({$ids})
                    GROUP BY xas.userid)tt
                ";

        $bind = array(
            ":courseid" => $course->id);

        return Dao::queryValue($sql, $bind);
    }
}
