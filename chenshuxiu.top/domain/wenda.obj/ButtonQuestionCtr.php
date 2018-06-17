<?php

class ButtonQuestionCtr extends SingleChoiceQuestionCtr
{

    public function getHtml () {
        return $this->getButtonHtml();
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
                </div>
                </div>
            </div>
INPUTHTML;
    }

    public function getScaleHtml () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $str = HtmlCtr::getBtnRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer());

        return <<< INPUTHTML
            <div class="sheet-item">
                <p class="sheet-item-title">{$this->getQuestionContent()} {$this->getStartHtml()}</p>
                <div class="mt5 clearfix">
                $str
                </div>
            </div>
INPUTHTML;
    }

    public function getHtml4hwk () {
        return $this->getRadioHtml4hwk();
    }

    protected function getButtonHtml () {
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

        $checkedOPtionId = $this->getTheXOptionIdOfAnswer();

        $str = HtmlCtr::getButtonRadioCtrImp($arr, '', $checkedOPtionId, $br, 'sheet-question-radio  btn default-btn succ-btn', $fixs);

        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            <div style="display: inline-block">
                <input type="hidden" name="{$this->getContentInputName()}" value="{$checkedOPtionId}">
                {$this->getStartHtml()}
                $str
                {$this->getQualitativeHtml()}
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

        $str = HtmlCtr::getButtonRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), $br, 'sheet-question-radio', $fixs, '<label class="radio-inline">', '</label>');

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

    public function getRadioHtml4scale () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();
        $str = HtmlCtr::getButtonRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), '', 'sheet-question-radio', $fixs);

        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            $str
INPUTHTML;
    }

    public function getRadioHtml4hwk () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();
        $str = HtmlCtr::getButtonRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), '<br/>', 'sheet-question-radio', $fixs);

        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            <br />
            $str
INPUTHTML;
    }

    public function getRadioHtml4Temp () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();
        $str = HtmlCtr::getButtonRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), '', 'sheet-question-radio', $fixs);

        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            <br />
            $str
INPUTHTML;
    }

    public function getHtml4History () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $str = HtmlCtr::getHistoryRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer());

        return <<< INPUTHTML
            <div class="sheet-item-history">
               <span class="questionpart">
                    {$this->getQuestionContentNoPos()}
                </span>
                <div class="answerpart">
                    $str
                </div>
            </div>
            <div style="clear:both"></div>
INPUTHTML;
    }

    public function getHtml4Bootstrap () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $str = '';

        $name = $this->getContentInputName();
        $selectid = $this->getTheXOptionIdOfAnswer();
        foreach ($arr as $key => $value) {
            $str .= ("\n<label class=\"checkbox-inline\" " ."><input type=\"radio\"
                     name=\"$name\" value=\"$key\" " . ($selectid == $key ? ' checked="checked"' : '') . " > $value</label>");
        }

        return <<< INPUTHTML
            <div class="form-group">
               <span class="col-sm-3">
                    {$this->getQuestionContentNoPos()}
                </span>
                <div class="col-sm-9">
                    {$str}
                </div>
            </div>
INPUTHTML;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtrCheckupTpl();

        $str = HtmlCtr::getHistoryRadioCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(),$fixs);

        return <<< INPUTHTML
            <div style="margin-bottom:20px; " class="{$inputClassDiv}">
                <div class="triangle-blue"></div>
                <span class="questionpart question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
                <div style="margin-bottom:20px;margin-left:20px;width:80%; ">
                    <div class="answerpart">
                        $str
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
}