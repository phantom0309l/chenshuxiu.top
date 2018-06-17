<?php

// 问题控件
abstract class QuestionCtr
{

    protected $xquestion = null;

    protected $xanswersheet = null;

    protected $xanswer = null;

    // $xquestion
    // $xanswersheet
    // $xanswer
    public function __construct (XQuestion $xquestion, $xanswersheet = null, $xanswer = null) {

        $this->xquestion = $xquestion;
        $this->xanswersheet = $xanswersheet;
        $this->xanswer = $xanswer;
    }

    // 创建 by 问题
    public static function createByXQuestion (XQuestion $xquestion, $xanswersheet = null) {
        $classname = $xquestion->type . "QuestionCtr";
        return new $classname($xquestion, $xanswersheet);
    }

    // 创建 by 答案
    public static function createByXAnswer (XAnswer $xanswer) {
        $classname = $xanswer->xquestion->type . "QuestionCtr";
        return new $classname($xanswer->xquestion, $xanswer->xanswersheet, $xanswer);
    }

    // 是答卷还是问卷
    private function getSheetType () {
        if ($this->hasXAnswerSheet()) {
            return 'XAnswerSheet';
        }
        return 'XQuestionSheet';
    }

    // 答卷id或问卷id
    private function getSheetId () {
        if ($this->hasXAnswerSheet()) {
            return $this->xanswersheet->id;
        }

        return $this->xquestion->xquestionsheet->id;
    }

    // 是否有xanswersheet
    private function hasXAnswerSheet () {
        return $this->xanswersheet instanceof XAnswerSheet;
    }

    // 是否有answer
    protected function hasXAnswer () {
        return $this->xanswer instanceof XAnswer;
    }

    // ##################################################

    // 问卷的填写 或 答卷修改 --begin--
    // 需要子类实现
    abstract public function getHtml ();

    // 子类可以重载
    public function getWenzhenHtml () {
        return $this->getHtml();
    }

    // 子类可以重载
    public function getScaleHtml () {
        return $this->getWenzhenHtml();
    }

    // 子类可以重载
    public function getHtml4hwk () {
        return $this->getHtml();
    }
    // 问卷的填写 或 答卷修改 --end--

    // ##################################################

    // 答卷浏览 --begin--
    // 量表样式,一般不需要重载
    public function getQaHtml4paper () {
        return $this->getQaHtml('<div style="padding: 10px;">', '</div>', '<div style="background: #eee; padding: 10px;">', '</div>');
    }

    // 课程样式,一般不需要重载
    public function getQaHtml4lesson () {
        $qcontentPre = '<p class="mainp_hwk">';
        $qcontentSuf = '</p>';
        $answerContentPre = '<p class="mainp_hwk" style="background: #eee; padding: 10px  0px 10px 10px;" >';
        $answerContentSuf = '</p>';
        return $this->getQaHtml($qcontentPre, $qcontentSuf, $answerContentPre, $answerContentSuf);
    }

    // 一问一答,不需要重载了
    public function getQaHtml ($qcontentPre = '', $qcontentSuf = '', $answerContentPre = '<br/>', $answerContentSuf = '') {

        $str = $qcontentPre; // 修正
        $str .= $this->getQuestionContent(); // 问题
        $str .= $qcontentSuf; // 修正
        $str .= $answerContentPre; // 修正
        $str .= $this->getQaHtmlAnswerContent(); // 答案
        $str .= $answerContentSuf; // 修正
        return $str;
    }

    public function getQaHtmlQuestionContent () {
        return $this->getQuestionContent();
    }

    // 答案部分 of 一问一答,子类可以重载, 多文本 和 picture 必须重载
    public function getQaHtmlAnswerContent () {

        $str = '';
        $answer1 = $this->getAnswerContent(1);

        if (empty($answer1)) {
            $str .= "答: 未填写";
        } else {
            if ($this->xquestion->type != "TextArea") {
                $str .= "答: ";
            }
            $str .= nl2br($this->getAnswerContent());
            $str .= " ";
            $str .= $this->xanswer->unit;
        }

        return $str;
    }

    // 获取答案文本数组, TODO by sjp 重复代码
    public function getAnswerContents () {
        $answer1 = $this->getAnswerContent(1);

        $str = '';
        if (! is_null($answer1)) {
            $str .= trim($this->getAnswerContent(1));
            $str .= " ";
            $str .= $this->xanswer->unit;
        }

        return array(
            $str);
    }
    // 答卷浏览 --end--

    // ##################################################

    // 生成 问题内容,答案内容,默认内容 --begin--

    // 获取当前问题,没有序号
    public function getQuestionContentNoPos () {
        return $this->xquestion->content . ' ' . $this->getNdHtml();
    }

    // 获取当前问题的描述,并考虑是否显示序号
    protected function getQuestionContent () {

        if ($this->xquestion->xquestionsheet->ishidepos || $this->xquestion->xquestionsheet->hasHideSubQuestions()) {
            return $this->getQuestionContentNoPos();
        }

        $str = '';
        $issub = $this->xquestion->issub;
        if ($this->xquestion->isCaption() && $issub == 1) {
            // 标题问题,且issub=1 不显示序号
        } elseif ($this->xquestion->isSection() && $issub == 1) {
            // 段落问题,且issub=1 不显示序号
        } elseif ($issub == 1) {
            $str = $this->xquestion->getPosFix() . ' ';
        } else {
            $str = $this->xquestion->getPosFix() . '. ';
        }
        return $str . nl2br($this->xquestion->content);
    }

    // 获取当前答案,content, 没有答案显示默认答案
    protected function getAnswerContent ($no = 1) {
        $k = "content{$no}";
        if ($this->hasXAnswer()) {
            if ($this->xanswer->isND()) {
                return 'ND';
            }

            // xanswer
            if ($no == 1) {
                $k = 'content';
            }

            return $this->xanswer->$k;
        }

        if ($this->xquestion->isYmd($no)) {
            return '';
        }

        // xquestion
        return $this->xquestion->$k;
    }

    // 生成 问题内容,答案内容,默认内容 --end--

    // ##################################################

    // 生成 input --begin--

    // 获取Content输入框name
    public function getContentInputName ($no = 1) {
        if ($no > 1) {
            $contentInputName = $this->getInputName();
            $contentInputName .= "[content{$no}]";
        } else {
            $contentInputName = $this->getInputName();
            if ($this->xquestion->isChoice()) {
                $contentInputName .= '[options][]';
            } else {
                $contentInputName .= '[content]';
            }
        }
        return trim($contentInputName);
    }

    // 获取other输入框name
    public function getOtherInputName () {
        $otherInputName = $this->getInputName() . '[content]';
        return trim($otherInputName);
    }

    // 获unit输入框name
    public function getUnitInputName () {
        return $this->getInputName() . '[unit]';
    }

    // 获取qualitative输入框name
    public function getQualitativeInputName () {
        return $this->getInputName() . '[qualitative]';
    }

    // 生成输入框的name
    private function getInputName () {
        return "sheets[{$this->getSheetType()}][{$this->getSheetId()}][{$this->xquestion->id}]";
    }

    // 获取输入框样式
    protected function getInputClass ($ctype) {
        return XQuestion::getContentInputClassByCtype($ctype);
    }

    // 获取输入框样式
    protected function getInputType ($ctype) {
        return XQuestion::getContentInputTypeByCtype($ctype);
    }

    // 获取当前默认值,目前只为日期类服务,否则返回空
    protected function getInputDataValue ($no = 1) {
        if (false == $this->xquestion->isYmd($no)) {
            return '';
        }

        $k = "content{$no}";
        if ($this->hasXAnswer()) {
            if ($no == 1) {
                $k = "content";
            }
            return $this->xanswer->$k;
        }

        return $this->xquestion->$k;
    }

    // 生成 input --end--

    // ##################################################

    // 问题辅助 --begin--

    // 获取unit下拉框html
    protected function getUnitHtml () {
        $units = $this->xquestion->getUnitArray();

        if (empty($units)) {
            return '';
        }

        if (count($units) == 1) {
            $unit = array_shift($units);
            return "<span class='sheet-question-unit'>{$unit}</span>";
        }

        $selectid = '';
        if ($this->hasXAnswer()) {
            $selectid = $this->xanswer->unit;
        }

        return HtmlCtr::getSelectCtrImp($units, $this->getUnitInputName(), $selectid, 'sheet-question-unit');
    }

    // 问题辅助，量化数据的定性
    protected function getQualitativeHtml () {
        $qualitatives = $this->xquestion->getQualitativeArray();

        if (empty($qualitatives)) {
            return '';
        }

        if (count($qualitatives) == 1) {
            $qualitative = array_shift($qualitatives);
            return "<span class='sheet-question-unit'>{$qualitative}</span>";
        }

        $selectid = '';
        if ($this->hasXAnswer()) {
            $selectid = $this->xanswer->qualitative;
        }

        return HtmlCtr::getSelectCtrImp($qualitatives, $this->getQualitativeInputName(), $selectid, 'sheet-question-qualitative');
    }

    // 获取提示信息
    protected function getTipHtml () {
        return '';
        $str = $this->xquestion->tip;
        if (empty($str)) {
            return '';
        }
        $str = "<span class=\"sheet-question-tip\">{$str}</span>";

        return $str;
    }

    // 获取带提示信息的html
    protected function getStartHtml () {
        $str = '';
        if ($this->xquestion->ismust) {
            $str = '<span style="color:#F00" class="sheet-question-start"> ( *必填 ) </span>';
        }

        return $str;
    }

    // 获取当前问题的提示
    protected function getQuestionTip () {
        $str = '';
        if ($this->xquestion->tip) {
            $str = "({$this->xquestion->tip})";
        }
        return $str;
    }

    // 问题辅助 --end--

    // ##################################################

    // 问卷/答卷 进行包装显示, 没有实际使用 --begin--
    // 简约型问题显示,包一个div
    public function getHtmlWithBox () {
        $str = $this->getHtml();
        return <<< INPUTHTML
        <div class="sheet-question-box {$this->xquestion->type} {$this->getSheetType()}-{$this->getSheetId()}-{$this->xquestion->id} " data-minvalue="{$this->xquestion->minvalue}" data-maxvalue="{$this->xquestion->maxvalue}">
            $str
            {$this->getTipHtml()}
        </div>
INPUTHTML;
    }

    // 获取带提示信息的html
    public function getHtmlWithTip () {
        return $this->getHtml() . $this->getTipHtml();
    }

    // 问卷/答卷 进行包装显示 --end--

    // ##############################################
    //

    public function getHtmlOfCheckupTpl4Admin () {
        return $this->getHtml();
    }

    public function getHtml4CheckupTplTable () {
        return $this->getQaHtml4paper();
    }

    public function getHtml4CheckupTpl () {
        return $this->getHtml();
    }

    public function getNdHtml () {
        if (! $this->xquestion->isShowND()) {
            return '';
        }
        $checked = '';
        if ($this->xanswer instanceof XAnswer && $this->xanswer->isND()) {
            $checked = 'checked="checked"';
        }
        return ' <input type="checkbox" class="input-nd" name="' . $this->getInputName() . '[isnd]" value="1" ' . $checked . ' >&nbsp;ND&nbsp;';

    }
}
