<?php

// 单行文本问题
class TextQuestionCtr extends OneTextBaseQuestionCtr
{

    public function getHtml4hwk () {
        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            <div style="margin-top:5px;">
               <input type="text" class="sheet-question-text fbt-text" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
            </div>
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
           <input type="text" class="answer-box" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
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

    public function getAnswerContentNoStyle () {
        return $this->getAnswerContent();
    }

    public function getHtml4History () {
        return <<< INPUTHTML
        <div class="sheet-item-history">
           <span class="questionpart">
                {$this->getQuestionContentNoPos()}
            </span>
            <div class="answerpart">
                <input type="text" placeholder="未填写" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
            </div>
        </div>
INPUTHTML;
    }

    public function getHtml4Bootstrap () {
        return <<< INPUTHTML
        <div class="form-group">
           <span class="col-sm-3">
                {$this->getQuestionContentNoPos()}
            </span>
            <div class="col-sm-9">
                <input type="text" class="form-control" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
            </div>
        </div>
INPUTHTML;
    }
}
