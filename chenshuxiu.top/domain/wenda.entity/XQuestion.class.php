<?php

/*
 * XQuestion
 */
class XQuestion extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xquestionsheetid',  // 问卷id
            'issimple',  // 是否是精简版问卷中的问题
            'issub',  // 是否子问题
            'pos',  // 序号
            'type',  // 问题类型:Num,Text,TextArea,SelectOption,SelectOptionWithOther,Radio,RadioWithOther,MultChoice,MultChoiceWithOther
            'ename',  // 英文名字,用于精确获取
            'prevtitle',  //
            'content',  // 问题内容,需要title吗?
            'text11',  // text11
            'content1',  // content1
            'text12',  // text12
            'ctype1',  // ctype1
            'text21',  // text21
            'content2',  // content2
            'text22',  // text22
            'ctype2',  // ctype2
            'text31',  // text31
            'content3',  // content3
            'text32',  // text32
            'ctype3',  // ctype3
            'text41',  // text41
            'content4',  // content4
            'text42',  // text42
            'ctype4',  // ctype4
            'text51',  // text51
            'content5',  // content5
            'text52',  // text52
            'ctype5',  // ctype5
            'tip',  // 问题提示
            'units',  // 单位,type=Num时使用
            'ismust',  // 是否必答
            'rightoptionid',  // 单选题的正确答案,type=SelectOption或Radio
            'minvalue',  // 最小值,type=Num 用于判定
            'maxvalue',  // 最大值,type=Num 用于判定
            'shownd',  // ND(not done),是否显示ND项 1显示 0不显示
            'qualitatives',  // 定性，类似units单位，逗号分隔存储
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xquestionsheetid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["xquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "xquestionsheetid");
        $this->_belongtos["rightoption"] = array(
            "type" => "XOption",
            "key" => "rightoptionid");
    }

    // 问题主类型定义
    public static function getTypeDescArray () {
        $arr = array();
        $arr['Caption'] = '标题';
        $arr['Section'] = '段落';

        $arr += self::getCtypeDescArray();

        $arr['TextArea'] = '多行文本';
        $arr['SelectOption'] = '下拉单选';
        $arr['SelectOptionWithOther'] = '下拉单选+其他';
        $arr['Radio'] = 'Radio单选';
        $arr['Button'] = '单选按钮';
        $arr['RadioWithOther'] = 'Radio单选+其他';
        $arr['MultChoice'] = '复选';
        $arr['MultChoiceWithOther'] = '复选+其他';
        $arr['Picture'] = '上传图片';
        $arr['TwoText'] = '双文本';
        $arr['ThreeText'] = '三文本';
        $arr['FourText'] = '四文本';
        $arr['FiveText'] = '五文本';

        return $arr;
    }

    // 输入框类型定义
    public static function getCtypeDescArray () {
        $arr = array();
        $arr['Text'] = '单行文本';
        $arr['Num'] = '数字';
        $arr['LongNum'] = '长数字';
        $arr['Y'] = '年';
        $arr['Ym'] = '年-月';
        $arr['Ymd'] = '年-月-日';
        $arr['Md'] = '月-日';
        $arr['Hi'] = '时-分';
        $arr['Province'] = '省';
        $arr['ProvinceCity'] = '省-市';
        return $arr;
    }

    // 获取样式
    public static function getContentInputClassByCtype ($ctype) {
        $arr = array();
        $arr['Text'] = 'sheet-question-text';
        $arr['Num'] = 'sheet-question-num';
        $arr['LongNum'] = 'sheet-question-longnum';
        $arr['Y'] = 'date-y';
        $arr['Ym'] = 'date-ym';
        $arr['Ymd'] = 'date-ymd';
        $arr['Md'] = 'date-md';
        $arr['Hi'] = 'date-hi';
        $arr['Province'] = 'sheet-question-province';
        $arr['ProvinceCity'] = 'sheet-question-provincecity';

        return $arr[$ctype];
    }

    // 获取样式
    public static function getContentInputTypeByCtype ($ctype) {
        if (in_array($ctype, array(
            'Num',
            'LongNum'))) {
            return 'number';
        }

        if (in_array($ctype, array(
            'Province',
            'ProvinceCity'))) {
            return 'hidden';
        }

        return 'text';
    }

    // 输入框类型描述
    public static function getCtypeDesc ($ctype) {
        $arr = self::getCtypeDescArray();
        return $arr[$ctype];
    }

    public function getTypeDesc () {
        $arr = self::getTypeDescArray();
        return $arr[$this->type];
    }

    public function getCtype1Desc () {
        return self::getCtypeDesc($this->ctype1);
    }

    public function getCtype2Desc () {
        return self::getCtypeDesc($this->ctype2);
    }

    public function getCtype3Desc () {
        return self::getCtypeDesc($this->ctype3);
    }

    public function getCtype4Desc () {
        return self::getCtypeDesc($this->ctype4);
    }

    public function getCtype5Desc () {
        return self::getCtypeDesc($this->ctype5);
    }

    public function getXQuestion () {
        return $this;
    }

    // 问题
    public function getPosFix () {

        // 只有一道题,不需要编号
        if ($this->xquestionsheet->getQuestionCnt() < 2) {
            return '';
        }

        // 将pos进行显示化处理
        $pos = $this->pos;
        $a = floor($pos);
        $x = intval($pos * 1000);
        $b = $x % 1000;
        if ($b < 900) {
            return $pos;
        } else {
            $c = $b % 100;
            $c += 9;
            return $a . "." . $c;
        }
    }

    public function getNext () {
        $pos = $this->pos + 1;
        return self::getByQuestionsheetidPos($this->xquestionsheetid, $pos);
    }

    public function getTitle () {
        return $this->content;
    }

    // 显示正确答案
    public function getRightOptionContent () {
        return $this->rightoption->content;
    }

    // 默认选中的选项id
    public function getDefaultCheckedOptionId () {
        $ids = $this->getDefaultCheckedOptionIds();

        if (empty($ids)) {
            return 0;
        }
        return array_shift($ids);
    }

    // 默认选中的选项数组
    public function getDefaultCheckedOptionIds () {
        $xoptions = $this->getOptions();
        $ids = array();
        foreach ($xoptions as $a) {
            if ($a->checked) {
                $ids[] = $a->id;
            }
        }

        return $ids;
    }

    // 简约型问题显示
    public function getHtml ($xanswersheet = null) {
        $ctr = $this->getQuestionCtr($xanswersheet);
        return $ctr->getHtmlWithBox();
    }

    // 简约型问题显示带提示
    public function getHtmlWithTip ($xanswersheet = null) {
        $ctr = $this->getQuestionCtr($xanswersheet);
        return $ctr->getHtmlWithTip();
    }

    // 获取对应的问题控件
    public function getQuestionCtr ($xanswersheet = null) {
        return QuestionCtr::createByXQuestion($this, $xanswersheet);
    }

    public static function getIsSubDescArray () {
        $arr = array();
        $arr[0] = '否';
        $arr[1] = '是';

        return $arr;
    }

    public function getIsSubDesc () {
        $arr = self::getIsSubDescArray();
        return $arr[$this->issub];
    }

    public static function getMustDescArray () {
        $arr = array();
        $arr[0] = '可空';
        $arr[1] = '必填';

        return $arr;
    }

    public function getIsMustDesc () {
        $arr = self::getMustDescArray();
        return $arr[$this->ismust];
    }

    public static function getSimpleDescArray () {
        $arr = array();
        $arr[0] = '否';
        $arr[1] = '是';

        return $arr;
    }

    public function getIsSimpleDesc () {
        $arr = self::getSimpleDescArray();
        return $arr[$this->issimple];
    }

    // 是否显示ND, shownd=1 且 非标题,非段落
    public function isShowND () {
        return $this->shownd == XConst::bool_yes && ! $this->isCaption() && ! $this->isSection();
    }

    public function getNdHtml ($xanswersheet = null) {
        if ($this->isCaption() || $this->isSection() || ! $this->isShowND()) {
            return '';
        }
        $ctr = $this->getQuestionCtr($xanswersheet);
        return $ctr->getNdHtml();
    }

    // 是标题
    public function isCaption () {
        return $this->type == 'Caption';
    }

    // 是段落
    public function isSection () {
        return $this->type == 'Section';
    }

    // 是数字题
    public function isNum ($ctype = 'type') {
        return $this->$ctype == 'Num' || $this->$ctype == 'LongNum';
    }

    // 是选择题
    public function isChoice () {
        return $this->isSingleChoice() || $this->isMultChoice();
    }

    // 是否是select
    public function isSelect () {
        $arr = array(
            'SelectOption',
            'SelectOptionWithOther',
        );
        return in_array($this->type, $arr);
    }

    // 是单选题
    public function isSingleChoice () {
        $arr = array(
            'SelectOption',
            'SelectOptionWithOther',
            'Radio',
            'RadioWithOther',
            'Button');
        return in_array($this->type, $arr);
    }

    // 是多选题
    public function isMultChoice () {
        $arr = array(
            'MultChoice',
            'MultChoiceWithOther');
        return in_array($this->type, $arr);
    }

    // 是日期题
    public function isYmd ($no = 1) {
        $arr = array(
            'Ymd',
            'Ym',
            'Y',
            'Md');
        if ($no < 2) {
            return in_array($this->type, $arr) || in_array($this->ctype1, $arr);
        }

        $ctype = "ctype{$no}";
        return in_array($this->$ctype, $arr);
    }

    // 多文本问题
    public function isMultText () {
        return in_array($this->type, array(
            'TwoText',
            'ThreeText',
            'FourText',
            'FiveText'));
    }

    // 多文本问题,子问题数
    public function getMultTextNum () {
        $arr = array();
        $arr['TwoText'] = 2;
        $arr['ThreeText'] = 3;
        $arr['FourText'] = 4;
        $arr['FiveText'] = 5;

        return isset($arr[$this->type]) ? $arr[$this->type] : 1;
    }

    // 多文本问题,子标题数组
    public function getMultTitles () {
        $titles = array();

        $num = $this->getMultTextNum();
        for ($i = 1; $i <= $num; $i ++) {
            $k1 = "text{$i}1";
            $k2 = "text{$i}2";
            $titles[] = $this->$k1 . " " . $this->$k2;
        }

        if (empty($titles)) {
            $titles[] = $this->content;
        }

        return $titles;
    }

    // 单位数组
    public function getUnitArray () {
        $units = explode(',', $this->units);

        $arr = array();
        foreach ($units as $a) {
            $a = trim($a);
            if (empty($a)) {
                continue;
            }
            $arr[$a] = $a;
        }
        return $arr;
    }

    // 定性数组
    public function getQualitativeArray () {
        $qualitatives = explode(',', $this->qualitatives);

        $arr = array();
        foreach ($qualitatives as $a) {
            $a = trim($a);
            if (empty($a)) {
                continue;
            }
            $arr[$a] = $a;
        }
        return $arr;
    }
    // 选项列表
    private $options = null;
    // 选项列表 简写
    public function getOptions () {
        if ($this->options === null) {
            $this->options = XOption::getArrayOfXquestion($this);
        }
        return $this->options;
    }

    // getOptionArray4HtmlCtr
    public function getOptionArray4HtmlCtr () {
        return XOption::toArray4HtmlCtr($this->getOptions());
    }

    // getOptionArrayHasScore4HtmlCtr
    public function getOptionArrayHasScore4HtmlCtr () {
        $arr = array();
        $xoptions = $this->getOptions();
        foreach ($xoptions as $a) {
            $arr[$a->id] = $a->content . "[{$a->score}]";
        }
        return $arr;
    }

    // 是最后一个问题
    public function isLastQuestion () {
        return $this->pos >= $this->xquestionsheet->getQuestionCnt();
    }

    // 默认隐藏
    public function isDefaultHide () {
        return $this->xquestionsheet->isDefaultHideEname($this->ename);
    }

    // 调试用途,父问题
    public function _getParentQuestionIdEname () {
        $q = $this->getParentQuestion();
        if ($q instanceof XQuestion) {
            return "[{$q->id},{$q->ename}]";
        }
        return '[,]';
    }

    // 调试用途,父问题
    public function _getSubQuestionIdEnameStr () {
        $qs = $this->getSubQuestions();
        $str = '{';
        foreach ($qs as $q) {
            $str .= "[{$q->id},{$q->ename}]";
        }
        $str .= '}';
        return $str;
    }

    // 获取父问题,没用到
    public function getParentQuestion () {
        return $this->xquestionsheet->getParentQuestionByQuestion($this);
    }

    // 子问题数组
    private $subQuestions = null;
    // 获取子问题数组
    public function getSubQuestions () {
        if (false == $this->isChoice()) {
            return array();
        }

        if ($this->subQuestions !== null) {
            return $this->subQuestions;
        }

        $subEnameArray = $this->getSubEnameArray();
        $questions = $this->xquestionsheet->getQuestions();

        $arr = array();
        foreach ($questions as $q) {
            if (in_array($q->ename, $subEnameArray)) {
                $arr[] = $q;
            }
        }
        $this->subQuestions = $arr;
        return $arr;
    }

    // 子问题ename array
    private $subEnameArray = null;
    // 获取子问题ename array
    public function getSubEnameArray () {
        if ($this->subEnameArray !== null) {
            return $this->subEnameArray;
        }

        if (false == $this->isChoice()) {
            return array();
        }

        $arr = array();
        foreach ($this->getOptions() as $o) {
            $arr1 = $o->getSubEnameArray();
            $arr = array_merge($arr, $arr1);
        }
        // 去重
        $this->subEnameArray = array_unique($arr);

        return $this->subEnameArray;
    }

    // 孩子今天有什么进步吗?
    public function isFbtHwkJinbuQuestion () {
        if ($this->type == 'TextArea' && strpos($this->content, '进步') > 0) {
            return true;
        }

        return false;
    }

    public function copyOne ($xquestionsheetNew) {
        $row = array();
        $row['xquestionsheetid'] = $xquestionsheetNew->id;
        $row['issimple'] = $this->issimple;
        $row['issub'] = $this->issub;
        $row['pos'] = $this->pos;
        $row['type'] = $this->type;
        $row['ename'] = $this->ename;
        $row['prevtitle'] = $this->prevtitle;
        $row['content'] = $this->content;
        $row['text11'] = $this->text11;
        $row['content1'] = $this->content1;
        $row['text12'] = $this->text12;
        $row['ctype1'] = $this->ctype1;
        $row['text21'] = $this->text21;
        $row['content2'] = $this->content2;
        $row['text22'] = $this->text22;
        $row['ctype2'] = $this->ctype2;
        $row['text31'] = $this->text31;
        $row['content3'] = $this->content3;
        $row['text32'] = $this->text32;
        $row['ctype3'] = $this->ctype3;
        $row['text41'] = $this->text41;
        $row['content4'] = $this->content4;
        $row['text42'] = $this->text42;
        $row['ctype4'] = $this->ctype4;
        $row['text51'] = $this->text51;
        $row['content5'] = $this->content5;
        $row['text52'] = $this->text52;
        $row['ctype5'] = $this->ctype5;
        $row['tip'] = $this->tip;
        $row['units'] = $this->units;
        $row['ismust'] = $this->ismust;
        $row['rightoptionid'] = $this->rightoptionid;
        $row['minvalue'] = $this->minvalue;
        $row['maxvalue'] = $this->maxvalue;
        $row['shownd'] = $this->shownd;
        $row['qualitatives'] = $this->qualitatives;
        $row['status'] = $this->status;

        $xquestionNew = self::createByBiz($row);
        $xoptions = $this->getOptions();
        foreach ($xoptions as $xoption) {
            $xoption->copyOne($xquestionNew);
        }
    }

    public function getCntOfXanswer () {
        $sql = 'SELECT COUNT(*) FROM xanswers WHERE xquestionid=:xquestionid';
        $bind = array(
            ':xquestionid' => $this->id);
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    // $row = array();
    // $row["xquestionsheetid"] = $xquestionsheetid;
    // $row["issub"] = $issub;
    // $row["pos"] = $pos;
    // $row["type"] = $type;
    // $row["ename"] = $ename;
    // $row["content"] = $content;
    // $row["tip"] = $tip;
    // $row["units"] = $units;
    // $row["ismust"] = $ismust;
    // $row["rightoptionid"] = $rightoptionid;
    // $row["minvalue"] = $minvalue;
    // $row["maxvalue"] = $maxvalue;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XQuestion::createByBiz row cannot empty");

        $default = array();
        $default["xquestionsheetid"] = 0;
        $default["issimple"] = 1;
        $default["issub"] = 0;
        $default["pos"] = 0;
        $default["type"] = '';
        $default["ename"] = '';
        $default["prevtitle"] = '';
        $default["content"] = '';
        $default["text11"] = '';
        $default["content1"] = '';
        $default["text12"] = '';
        $default["ctype1"] = '';
        $default["text21"] = '';
        $default["content2"] = '';
        $default["text22"] = '';
        $default["ctype2"] = '';
        $default["text31"] = '';
        $default["content3"] = '';
        $default["text32"] = '';
        $default["ctype3"] = '';
        $default["text41"] = '';
        $default["content4"] = '';
        $default["text42"] = '';
        $default["ctype4"] = '';
        $default["text51"] = '';
        $default["content5"] = '';
        $default["text52"] = '';
        $default["ctype5"] = '';
        $default["tip"] = '';
        $default["units"] = '';
        $default["ismust"] = 0;
        $default["rightoptionid"] = 0;
        $default["minvalue"] = 0;
        $default["maxvalue"] = 0;
        $default["shownd"] = 0;
        $default["qualitatives"] = '';
        $default["status"] = 1;

        $row += $default;

        $row['ename'] = FUtil::filterInvisibleChar($row['ename']);

        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 问题 by ename
    public static function getByEname ($ename) {
        $cond = "AND ename=:ename";

        $bind = array(
            ':ename' => $ename);

        return Dao::getEntityByCond('XQuestion', $cond, $bind);
    }

    // 问题 by ename and xquestionsheetid
    public static function getByEnameAndXQuestionSheetid ($ename, $xquestionsheetid) {
        $cond = "AND ename=:ename AND xquestionsheetid=:xquestionsheetid";

        $bind = array(
            ':ename' => $ename,
            ':xquestionsheetid' => $xquestionsheetid);

        return Dao::getEntityByCond('XQuestion', $cond, $bind);
    }

    public static function getByXQuestionSheetEname (XQuestionSheet $xquestionsheet, $ename) {
        $cond = "AND xquestionsheetid=:xquestionsheetid AND ename=:ename ";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id,
            ':ename' => $ename);

        return Dao::getEntityByCond('XQuestion', $cond, $bind);
    }

    // 问题列表 of 问卷
    public static function getArrayOfXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $cond = "AND xquestionsheetid=:xquestionsheetid order by pos ";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id);

        return Dao::getEntityListByCond('XQuestion', $cond, $bind);
    }

    // 问题数 of 问卷
    public static function getCntOfXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $sql = "select count(*) as cnt
                from xquestions
                where xquestionsheetid=:xquestionsheetid ";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id);

        return Dao::queryValue($sql, $bind);
    }

    // 最大序号 of 问卷
    public static function getMaxPosOfXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $sql = "select max(pos) as maxpos
                from xquestions
                where xquestionsheetid=:xquestionsheetid ";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id);

        return Dao::queryValue($sql, $bind);
    }

    // 第一个问题 of 问卷
    public static function getFirstOneByXQuestionSheet (XQuestionSheet $xquestionsheet) {
        $cond = "AND xquestionsheetid=:xquestionsheetid order by pos limit 1 ";

        $bind = array(
            ':xquestionsheetid' => $xquestionsheet->id);

        return Dao::getEntityByCond('XQuestion', $cond, $bind);
    }

    public static function getByQuestionsheetidPos ($questionsheetid, $pos) {
        $cond = " AND xquestionsheetid = :xquestionsheetid AND pos = :pos ";

        $bind = array(
            ":xquestionsheetid" => $questionsheetid,
            ":pos" => $pos);

        return Dao::getEntityByCond('XQuestion', $cond, $bind);
    }
}
