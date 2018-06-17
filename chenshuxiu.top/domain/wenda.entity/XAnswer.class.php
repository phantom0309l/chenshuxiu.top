<?php

/*
 * XAnswer
 */
class XAnswer extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xanswersheetid',  // 答卷id
            'xquestionid',  // 问题id
            'pos',  // pos快照
            'content',  // 答案内容,text
            'content2',  // 双输入框问题的第二部分内容
            'content3',  // 多输入框问题
            'content4',  // 多输入框问题
            'content5',  // 多输入框问题
            'text11',  // text11
            'text12',  // text12
            'text21',  // text21
            'text22',  // text22
            'text31',  // text31
            'text32',  // text32
            'text41',  // text41
            'text42',  // text42
            'text51',  // text51
            'text52',  // text52
            'unit',  // 单位
            'qualitative',  // 定性
            'isright',  // 是否正确值:单选和数字类型时判定
            'isnd',  // 是否是ND 1是 0不是
            'score'); // 得分
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xanswersheetid',
            'xquestionid',
            'pos');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["xanswersheet"] = array(
            "type" => "XAnswerSheet",
            "key" => "xanswersheetid");
        $this->_belongtos["xquestion"] = array(
            "type" => "XQuestion",
            "key" => "xquestionid");
    }

    public function getContent1 () {
        return $this->content;
    }

    // 简约型问题显示
    public function getHtml () {
        $ctr = $this->getQuestionCtr();
        return $ctr->getHtmlWithBox();
    }

    // 简约型问题显示带提示
    public function getHtmlWithTip () {
        $ctr = $this->getQuestionCtr();
        return $ctr->getHtmlWithTip();
    }

    // 获取对应的问题控件,$xanswersheet用不到
    public function getQuestionCtr ($xanswersheet = null) {
        return QuestionCtr::createByXAnswer($this);
    }

    // 多个选项关系
    private $xAnswerOptionRefs = null;
    // 多个选项关系
    public function getXAnswerOptionRefs () {
        if (false == is_array($this->xAnswerOptionRefs)) {
            $this->xAnswerOptionRefs = XAnswerOptionRef::getArrayOfXAnswer($this);
        }
        return $this->xAnswerOptionRefs;
    }

    // 单个选项关系
    public function getXAnswerOptionRef () {
        return XAnswerOptionRef::getOneByXAnswer($this);
    }

    // 单选中的那个选项(多选请不要调用)
    public function getTheXOption () {
        $ref = $this->getXAnswerOptionRef();
        return $ref->xoption;
    }

    // 选中项数组(可能是单选或多选)
    public function getTheXOptions () {
        $refs = $this->getXAnswerOptionRefs();

        $arr = array();
        foreach ($refs as $ref) {
            $arr[] = $ref->xoption;
        }

        return $arr;
    }

    public function getPicture () {
        $id = (int) $this->content;
        if (! is_int($id))
            return;
        return Picture::getById($id);
    }

    public function getTitle () {
        return $this->xquestion->getTitle();
    }

    public function toJsonArrayNew () {
        $list = $this->toJsonArray();

        if ($this->xquestiontype == 'Picture') {
            $pictureids = explode(',', $this->content);

            $arr = [];
            foreach ($pictureids as $pictureid) {
                if (!$pictureid) {
                    continue;
                }
                $picture = Picture::getById($pictureid);
                $tmp = [];

                $tmp['id'] = $pictureid;
                $tmp['url'] = $picture->getSrc();

                $arr[] = $tmp;
            }

            $list['content'] = $arr;
        }

        return $list;
    }

    // 以显示的enames
    private $showSubEnameArray = null;
    // 获取可以显示的enames
    public function getShowSubEnameArray () {
        if (is_array($this->showSubEnameArray)) {
            return $this->showSubEnameArray;
        }

        $arr = array();

        $refs = $this->getTheXOptions();
        foreach ($refs as $ref) {
            $arr1 = $ref->getShowEnameArray();
            $arr = array_merge($arr, $arr1);
        }

        $arr = array_unique($arr);
        $this->showSubEnameArray = $arr;

        return $arr;
    }

    // 获取父答案
    public function getParentAnswer () {
        $arr = $this->xanswersheet->getAnswers();
        $parentQuestion = $this->xquestion->getParentQuestion();
        foreach ($arr as $a) {
            if ($a->xquestionid == $parentQuestion->id) {
                return $a;
            }
        }

        return null;
    }

    public function isND () {
        return $this->isnd == XConst::bool_yes;
    }

    // 默认隐藏
    public function isDefaultHide () {
        $parentAnswer = $this->getParentAnswer();

        if ($parentAnswer instanceof XAnswer && in_array($this->xquestion->ename, $parentAnswer->getShowSubEnameArray())) {
            return false;
        }

        // TODO by sjp : 这一行可能不需要
        return $this->xanswersheet->xquestionsheet->isDefaultHideEname($this->xquestion->ename);
    }

    // 没有结果
    public function noContent () {
        if ($this->xquestion->isChoice()) {
            $refs = $this->getXAnswerOptionRefs();
            return empty($refs);
        }

        return $this->content == '';
    }

    private $_xAnswerOptionRef = null;

    public function getOneOptionRef () {
        return $this->_xAnswerOptionRef;
    }

    public function setOneOptionRef ($ref) {
        $this->_xAnswerOptionRef = $ref;
    }

    // 修正数字
    public function fixContent4Num () {
        if ($this->xquestion->isNum('type') && $this->content == '') {
            $this->content = 0;
        } elseif ($this->xquestion->isMultText()) {

            if ($this->xquestion->isNum('ctype1') && $this->content == '') {
                $this->content = 0;
            }

            if ($this->xquestion->isNum('ctype2') && $this->content2 == '') {
                $this->content2 = 0;
            }

            if ($this->xquestion->isNum('ctype3') && $this->content3 == '') {
                $this->content3 = 0;
            }

            if ($this->xquestion->isNum('ctype4') && $this->content4 == '') {
                $this->content4 = 0;
            }

            if ($this->xquestion->isNum('ctype5') && $this->content5 == '') {
                $this->content5 = 0;
            }
        }
    }

    public function removeXanswerOptionRefsAndScore () {
        $xanswersheet = $this->xanswersheet;
        //减去得分
        $xanswersheet->score -= $this->score;
        $this->score = 0;
        $xanswerOptionRefs = $this->getXAnswerOptionRefs();
        foreach ($xanswerOptionRefs as $ref) {
            $ref->remove();
        }
        $this->isright = 0;
    }

    public function addXAnswerOptionRefsAndScore ($xoptions) {
        $q = $this->xquestion;
        foreach ($xoptions as $xoption) {
            $xansweroptionref = XAnswerOptionRef::createBy2Entity($this, $xoption);
            $this->setOneOptionRef($xansweroptionref);
            if ($xoption->id == $q->rightoptionid) {
                $this->isright = 1;
            }
            $this->score += $xoption->score;
        }
        $xanswersheet = $this->xanswersheet;
        // 修正得分
        $xanswersheet->score += $this->score;
    }

    public function copyOne (XAnswerSheet $xanswersheetnew) {
        $row = array();
        $row['xanswersheetid'] = $xanswersheetnew->id;
        $row['xquestionid'] = $this->xquestionid;
        $row['pos'] = $this->pos;
        $row['content'] = $this->content;
        $row['content2'] = $this->content2;
        $row['content3'] = $this->content3;
        $row['content4'] = $this->content4;
        $row['content5'] = $this->content5;
        $row['text11'] = $this->text11;
        $row['text21'] = $this->text21;
        $row['text31'] = $this->text31;
        $row['text41'] = $this->text41;
        $row['text51'] = $this->text51;
        $row['text12'] = $this->text12;
        $row['text22'] = $this->text22;
        $row['text32'] = $this->text32;
        $row['text42'] = $this->text42;
        $row['text52'] = $this->text52;
        $row['unit'] = $this->unit;
        $row['isright'] = $this->isright;
        $row['isnd'] = $this->isnd;
        $row['qualitative'] = $this->qualitative;
        $row['score'] = $this->score;

        $xanswerNew = self::createByBiz($row);
        $xansweroptionrefs = $this->getXAnswerOptionRefs();
        foreach ($xansweroptionrefs as $xansweroptionref) {
            $xansweroptionref->copyOne($xanswerNew);
        }
    }

    // $row = array();
    // $row["xanswersheetid"] = $xanswersheetid;
    // $row["xquestionid"] = $xquestionid;
    // $row["pos"] = $pos;
    // $row["content"] = $content;
    // $row["unit"] = $unit;
    // $row["isright"] = $isright;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XAnswer::createByBiz row cannot empty");

        $default = array();
        $default["xanswersheetid"] = 0;
        $default["xquestionid"] = 0;
        $default["pos"] = 0;
        $default["content"] = '';
        $default["content2"] = '';
        $default["content3"] = '';
        $default["content4"] = '';
        $default["content5"] = '';
        $default["text11"] = '';
        $default["text21"] = '';
        $default["text31"] = '';
        $default["text41"] = '';
        $default["text51"] = '';
        $default["text12"] = '';
        $default["text22"] = '';
        $default["text32"] = '';
        $default["text42"] = '';
        $default["text52"] = '';
        $default["unit"] = '';
        $default["isright"] = 0;
        $default["isnd"] = 0;
        $default["qualitative"] = '';
        $default["score"] = 0;

        $row += $default;
        $xanswer = new self($row);

        return $xanswer;
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 指定答案 of 答卷
    public static function getByXQuestionIdOfXAnswerSheet (XAnswerSheet $xanswersheet, $xquestionid) {
        $cond = "AND xanswersheetid=:xanswersheetid AND xquestionid=:xquestionid ";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheet->id;
        $bind[':xquestionid'] = $xquestionid;

        return Dao::getEntityByCond('XAnswer', $cond, $bind);
    }

    // 答案列表 of 答卷
    public static function getArrayOfXAnswerSheet (XAnswerSheet $xanswersheet) {
        $cond = "AND xanswersheetid=:xanswersheetid order by pos";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheet->id;

        return Dao::getEntityListByCond('XAnswer', $cond, $bind);
    }

    public static function getListByXAnswerSheet ($xanswersheetid) {
        $cond = "AND xanswersheetid=:xanswersheetid order by pos";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheetid;

        return Dao::getEntityListByCond('XAnswer', $cond, $bind);
    }

    // 已做答案数 of 答卷
    public static function getCntOfXAnswerSheet (XAnswerSheet $xanswersheet) {
        $sql = "select count(*) as cnt from xanswers where xanswersheetid=:xanswersheetid ";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheet->id;

        return Dao::queryValue($sql, $bind);
    }

    // 已做正确答案数 of 答卷
    public static function getRightCntOfXAnswerSheet (XAnswerSheet $xanswersheet) {
        $sql = "select count(*) as cnt from xanswers where xanswersheetid=:xanswersheetid and isright=1 ";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheet->id;

        return Dao::queryValue($sql, $bind);
    }

    // 已做错误答案数 of 答卷
    public static function getErrorCntOfXAnswerSheet (XAnswerSheet $xanswersheet) {
        $sql = "select count(*) as cnt from xanswers where xanswersheetid=:xanswersheetid and isright=0 ";

        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheet->id;

        return Dao::queryValue($sql, $bind);
    }

    // 获取SNAP-IV评估较严重情况答案数
    public static function getADHDIVCntOfSerious (XAnswerSheet $xanswersheet, $glt, $baseNum) {
        $bind = [];
        $bind[':pos'] = $baseNum;

        $pos = "t.pos < :pos";

        if ($glt == "gt") {
            $pos = "t.pos > :pos";
        }

        $sql = "select count(*) as cnt from
(select * from xanswers where xanswersheetid=:xanswersheetid)t
inner join `xansweroptionrefs` b
on t.id = b.xanswerid
where {$pos} and (b.content = '常常' or b.content = '总是')";

        $bind[':xanswersheetid'] = $xanswersheet->id;

        return Dao::queryValue($sql, $bind);
    }

    public static function getXAnswerByXQuestionename (Paper $paper, $ename) {
        $xanswers = $paper->getAnswers();
        foreach ($xanswers as $a) {
            $xquestion = XQuestion::getById($a->xquestionid);
            if ($xquestion->ename == $ename) {
                $xanswer = $a;
                break;
            }
        }

        return $xanswer;
    }

    public static function getAnswer ($xquestionsheetid, $patientid, $xquestionid) {
        $bind = [];
        $bind[':xquestionsheetid'] = $xquestionsheetid;
        $bind[':patientid'] = $patientid;

        $xanswersheet = Dao::getEntityByCond("XAnswerSheet", " and xquestionsheetid = :xquestionsheetid and patientid = :patientid ", $bind);

        if ($xanswersheet instanceof XAnswerSheet) {
            $bind = [];
            $bind[':xanswersheetid'] = $xanswersheet->id;
            $bind[':xquestionid'] = $xquestionid;

            $xanswer = Dao::getEntityByCond("XAnswer", " and xanswersheetid = :xanswersheetid and xquestionid = :xquestionid", $bind);
            return $xanswer->content;
        } else {
            return '';
        }
    }

    public static function getAnswerByXanswersheetidXquestionid ($xanswersheetid, $xquestionid) {
        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheetid;
        $bind[':xquestionid'] = $xquestionid;

        $xanswer = Dao::getEntityByCond("XAnswer", " and xanswersheetid = :xanswersheetid and xquestionid = :xquestionid", $bind);

        if ($xanswer instanceof XAnswer) {
            return $xanswer->getQuestionCtr()->getHtml4CheckupTplTable_fixdb();
        } else {
            return '';
        }
    }
}
