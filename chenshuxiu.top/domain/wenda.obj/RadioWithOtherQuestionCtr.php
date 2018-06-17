<?php

class RadioWithOtherQuestionCtr extends RadioQuestionCtr
{

    public function getHtml () {
        $str = $this->getRadioHtml();
        $html = <<< INPUTHTML
            $str
            {$this->getOtherInner()}
            *
INPUTHTML;
        return $html;
    }

    public function getOtherInner () {
        $inputName = $this->getOtherInputName();

        $ctype = $this->xquestion->ctype1;
        $inputType = $this->getInputType($ctype);
        $inputClass = $this->getInputClass($ctype);

        $html = <<< INPUTHTML
            {$this->xquestion->text11}
            <input type="{$inputType}" class="sheet-input {$inputClass}" name='{$inputName}' value="{$this->getAnswerContent()}"/>
            {$this->xquestion->text12}
INPUTHTML;
        return $html;
    }

    public function getWenzhenHtml () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $str = HtmlCtr::getWenzhenRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer());

        return <<< INPUTHTML
            <div class="sheet-item">
                <p class="sheet-item-title">{$this->getQuestionContent()} {$this->getStartHtml()}</p>
                <div class="mt5 clearfix">
                <div class="radioBox">
                    $str
                    <div class="radio-item">
                        {$this->getOtherInner()}
                    </div>
                </div>
                </div>
            </div>
INPUTHTML;
    }

    public function getScaleHtml () {
        $str = $this->getRadioHtml4scale();
        $inputName = $this->getOtherInputName();
        $html = <<< INPUTHTML
            $str
            <input type="text" class="sheet-question-text-other sheet-input wh35" name='{$inputName}' value="{$this->getAnswerContent()}"/>
INPUTHTML;
        return $html;
    }

    public function getHtml4hwk () {

        $str = $this->getRadioHtml4hwk();

        $inputName = $this->getOtherInputName();

        $html = <<< INPUTHTML
            $str
            <input type="text" class="sheet-question-text-other" name='{$inputName}' value="{$this->getAnswerContent()}"/>
INPUTHTML;
        return $html;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtrCheckupTpl();
        $str = HtmlCtr::getHistoryRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(),$fixs);
        $inputName = $this->getOtherInputName();

        return <<< INPUTHTML
            <div style="margin-bottom:20px;" class="{$inputClassDiv}">
                <div class="triangle-blue"></div>
                <span class="questionpart question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
                <div style="margin-bottom:20px;margin-left:20px;width:80%;position:relative;top:20px; ">
                    <div class="answerpart">
                        $str
                        <input type="text" class="answer-box" style="position:relative;top:-20px;" name='{$inputName}' value="{$this->getAnswerContent()}"/>
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
INPUTHTML;
    }

    public function getHtml4CheckupTplTable () {
        $str= '';
        foreach( $this->getAnswerContents() as $t ){
            $str .= $t;
        }
        return <<< INPUTHTML
            <div class="table-block">
                <div class="table-block-key">{$this->getQuestionContentNoPos()}</div>
                <div class="table-block-value">
                    {$str}
                </div>
            </div>
INPUTHTML;
    }

    public function getHtmlOfCheckupTpl4Admin ($horizontal = true) {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();

        $br = ' ';
        $str = '';
        foreach ($arr as $a) {
            $str .= $a;
        }

        if (mb_strlen($str) > 14) {
            $br = '<br/>';
        }

        $str = HtmlCtr::getRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), $br, 'sheet-question-radio', $fixs, '<label class="radio-inline">', '</label>');

        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        return <<< INPUTHTML
            <div class="form-group {$inputClassDiv}">
            <label class="sheet-question-content control-label col-md-2 col-sm-2">{$this->getQuestionContent()} {$this->getStartHtml()}</label>
            <div class="col-md-10 col-sm-10">
            $str
            </div>
            </div>
INPUTHTML;
    }

}
