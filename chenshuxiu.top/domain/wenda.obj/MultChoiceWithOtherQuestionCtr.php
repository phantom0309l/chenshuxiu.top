<?php

class MultChoiceWithOtherQuestionCtr extends MultChoiceQuestionCtr
{

    public function getHtml () {
        $str = $this->getCheckboxHtml();

        $inputName = $this->getOtherInputName();

        $html = <<< INPUTHTML
            $str
            <input type="text" class="sheet-question-text-other" name='{$inputName}' value="{$this->getAnswerContent()}"/>
INPUTHTML;
        return $html;
    }

    public function getWenzhenHtml () {
        $inputName = $this->getOtherInputName();
        $str = <<< INPUTHTML
        <div class="checkbox-item">
            <input type="text" class="sheet-question-text sheet-input" name='{$inputName}' value="{$this->getAnswerContent()}"/>
            </div>
INPUTHTML;
        return $this->getCheckboxHtml4wenzhen($str);
    }

    public function getHtml4CheckupTpl () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckupTplCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer());

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
                <div style="margin-bottom:20px;margin-left:20px;width:80%; ">
                    <div class="checkBox" style='padding:7px 10px 7px 10px;width:auto;'>
                        {$checkBoxStr}
                    </div>
                        <input type="text" class="answer-box" name='{$this->getOtherInputName()}' value="{$this->getAnswerContent()}"/>
                    </div>
            </div>
            <div style="clear:both;"></div>
INPUTHTML;
    }

    public function getHtml4CheckupTplTable () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckboxCtrTableImp($arr, $this->getTheXOptionIdsOfAnswer());

        return <<< INPUTHTML
            <div class="table-block">
                <div class="table-block-key">{$this->getQuestionContentNoPos()}</div>
                <div class="table-block-value">
                    {$checkBoxStr}
                </div>
            </div>
INPUTHTML;
    }
}
