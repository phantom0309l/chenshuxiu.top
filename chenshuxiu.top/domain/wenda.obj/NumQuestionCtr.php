<?php

// 短数字问题
class NumQuestionCtr extends OneTextBaseQuestionCtr
{

    public function getHtml () {
        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            <div style="margin-top:5px;line-height:150%">
                <input type="number" step="0.01" class="sheet-question sheet-input sheet-question-num" name='{$this->getContentInputName()}' value='{$this->getAnswerContent()}'/> {$this->getUnitHtml()}
            </div>
INPUTHTML;
    }

    public function getHtml4hwk () {
        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            <br />
            <input type="number" step="0.01" class="sheet-question-num fbt-num" name='{$this->getContentInputName()}' value='{$this->getAnswerContent()}'/> {$this->getUnitHtml()}
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
           <input type="number" step="0.01" class="answer-box" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
        </div>
INPUTHTML;
    }

    public function getHtmlOfCheckupTpl4Admin ($horizontal = true) {
        $unitHtml = $this->getUnitHtml();
        if ($unitHtml) {
            $unitHtml = '<span class="input-group-addon">' . $unitHtml . '</span>';
        }
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }
        if ($unitHtml) {
            return <<< INPUTHTML
                <div class="form-group {$inputClassDiv}">
                <label class="control-label col-md-2 col-sm-2">
        {$this->getQuestionContentNoPos()}
        </label>
        <div class="col-md-10 col-sm-10 controls">
        <div class="input-group">
        <input type="number" step="0.01" class="form-control" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/>{$unitHtml}
        </div>
        </div>
        </div>
INPUTHTML;
        } else {
            return <<< INPUTHTML
                <div class="form-group {$inputClassDiv}">
                <label class="control-label col-md-2 col-sm-2">
        {$this->getQuestionContentNoPos()}
        </label>
        <div class="col-md-10 col-sm-10">
        <input type="number" step="0.01" class="form-control" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/>
        </div>
        </div>
INPUTHTML;
        }
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

    public function getHtml4CheckupTplTableValue () {
        return $this->getAnswerContent();
    }

    //给脚本用的
    public function getHtml4CheckupTplTable_fixdb () {
        return $this->getAnswerContent();
    }
}
