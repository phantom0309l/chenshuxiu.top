<?php
// 下拉单选
class SelectOptionQuestionCtr extends SingleChoiceQuestionCtr
{

    public function getHtml () {
        return $this->getSelectHtml();
    }

    protected function getSelectHtml () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();
        $str = HtmlCtr::getSelectCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), 'sheet-question-select', '', $fixs);

        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            $str
            {$this->getQualitativeHtml()}
INPUTHTML;
    }

    public function getWenzhenHtml () {

        $arr = $this->getXOptionArray4HtmlCtr();
        $str = HtmlCtr::getSelectCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), 'sheet-question-select');

        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            $str
INPUTHTML;
    }

    public function getHtmlOfCheckupTpl4Admin () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }
        $arr = $this->getXOptionArray4HtmlCtr();
        $str = HtmlCtr::getSelectCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), 'sheet-question-select form-control');

        return <<< INPUTHTML
                <div class="form-group {$inputClassDiv}">
                <label class="control-label col-md-2 col-sm-2">
        {$this->getQuestionContentNoPos()}
        </label>
        <div class="col-md-10 col-sm-10">
            {$this->getStartHtml()}
            $str
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

        $str = HtmlCtr::getSelectCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), 'sheet-question-select answer-box newSelect-arrow');

        return <<< INPUTHTML
            <div style="margin-bottom:20px; " class="{$inputClassDiv}">
                <div class="triangle-blue"></div>
                <span class="questionpart question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
                $str
            </div>
            <div style="clear:both;"></div>
INPUTHTML;
    }
}
