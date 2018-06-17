<?php

class SelectOptionWithOtherQuestionCtr extends SelectOptionQuestionCtr
{

    public function getHtml () {
        $str = $this->getSelectHtml();
        $html = <<< INPUTHTML
            $str
            {$this->getOtherInner()}
INPUTHTML;
        return $html;
    }

    public function getOtherInner () {
        $inputName = $this->getOtherInputName();

        $ctype = $this->xquestion->ctype1;
        $inputType = $this->getInputType($ctype);
//         $inputClass = $this->getInputClass($ctype);

        $html = <<< INPUTHTML
            {$this->xquestion->text11}
            <input type="{$inputType}" class="sheet-question-text-other" name='{$inputName}' value="{$this->getAnswerContent()}"/>
            {$this->xquestion->text12}
INPUTHTML;
        return $html;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $arr = $this->getXOptionArray4HtmlCtr();

        $str = HtmlCtr::getSelectCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdOfAnswer(), 'sheet-question-select answer-box newSelect-arrow');

        $inputName = $this->getOtherInputName();

        $ctype = $this->xquestion->ctype1;
        $inputType = $this->getInputType($ctype);

        return <<< INPUTHTML
            <div style="margin-bottom:20px; " class="{$inputClassDiv}">
                <div class="triangle-blue"></div>
                <span class="questionpart question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
                $str
                {$this->xquestion->text11}
                <input type="{$inputType}" class="sheet-question-text-other answer-box" name='{$inputName}' value="{$this->getAnswerContent()}"/>
                {$this->xquestion->text12}
            </div>
            <div style="clear:both;"></div>
INPUTHTML;
    }
}
