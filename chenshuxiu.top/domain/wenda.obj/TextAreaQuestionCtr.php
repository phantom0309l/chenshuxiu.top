<?php

class TextAreaQuestionCtr extends QuestionCtr
{

    public function getHtml () {
        $str11 = '<span class="sheet-question-content">';
        $str12 = '</span>';
        $str21 = '<div style="margin-top:5px;"> <span style="vertical-align:top;">答：</span>';
        $str22 = '</div >';

        return $this->getHtmlImp($str11, $str12, $str21, $str22);
    }

    public function  getHtmlOfCheckupTpl4Admin() {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }
        return <<< INPUTHTML
        <div class="form-group {$inputClassDiv}">
            <label class="control-label col-md-2 col-sm-2">{$this->getQuestionContent()} </label>
            <div class="col-md-10 col-sm-10">
                <textarea rows=5 class='form-control sheet-input sheet-question-textarea' name="{$this->getContentInputName()}">{$this->getAnswerContent()}</textarea>
            </div>
            </div>
INPUTHTML;
    }

    public function getWenzhenHtml () {

        $str11 = '<div class="sheet-item"><p class="sheet-item-title">';
        $str12 = '';
        $str21 = '</p> <div class="mt5">';
        $str22 = '</div > </div>';

        return $this->getHtmlImp($str11, $str12, $str21, $str22);
    }

    public function getScaleHtml () {
        return $this->getWenzhenHtml();
    }

    private function getHtmlImp ($str11, $str12, $str21, $str22) {
        return <<< INPUTHTML
            {$str11}
            {$this->getQuestionContent()}
            {$str12}
            {$this->getStartHtml()}
            {$str21}
            <textarea class='sheet-input sheet-question-textarea' name="{$this->getContentInputName()}">{$this->getAnswerContent()}</textarea>
            {$str22}

INPUTHTML;
    }

    public function getHtml4hwk () {
        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            {$this->getStartHtml()}
            <br/>
            <textarea class='sheet-question-textarea fbt-ta' name="{$this->getContentInputName()}">{$this->getAnswerContent()}</textarea>
INPUTHTML;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        return <<< INPUTHTML
        <div style="margin-bottom:20px; "  class="{$inputClassDiv}">
           <div class="triangle-blue"></div>
           <span class="question-title">
                {$this->getQuestionContentNoPos()}
            </span>
            <div></div>
           <textarea type="text" style="margin:10px 0px 0px 30px;border:1px solid #3bacf9;width:80%;padding:5px;color:#3bacf9;"
           rows=5 name='{$this->getContentInputName()}'>{$this->getAnswerContent()}</textarea>
        </div>
INPUTHTML;
    }

    public function getHtml4CheckupTplTable () {
        return <<< INPUTHTML
            <div class="table-block">
                <div class="table-block-key">{$this->getQuestionContentNoPos()}</div>
                <div class="table-block-value">
                    {$this->getAnswerContent()}
                </div>
            </div>
INPUTHTML;
    }

    // public function getQaInputHtml4Fbt() {
    // return <<<HTML
    // <textarea class="fbt-ta" ms-if-loop='q.type=="String"'
    // ms-duplex-string='q.value'></textarea>
    // HTML;
    // }
}

