<?php

// 单文本输入框问题
class OneTextBaseQuestionCtr extends TextBaseQuestionCtr
{

    // input区, 可以重载
    protected function getInputHtml () {
        return $this->getOneInputHtml(1);
    }

}

// 长数字问题
class LongNumQuestionCtr extends OneTextBaseQuestionCtr
{
}

// 省
class ProvinceQuestionCtr extends OneTextBaseQuestionCtr
{
}

// 省-市
class ProvinceCityQuestionCtr extends OneTextBaseQuestionCtr
{
}

// 日期, 基类
class DateQuestionCtr extends OneTextBaseQuestionCtr
{
}

// 时-分
class HiQuestionCtr extends DateQuestionCtr
{

}

// 年-月-日
class YmdQuestionCtr extends DateQuestionCtr
{
    public function getHtml4History () {
        return <<< INPUTHTML
            <div class="sheet-item-history">
                <span class="questionpart">{$this->getQuestionContentNoPos()}</span>
                <div class="answerpart">
                   <input type="text" readonly class="datectr" placeholder="未填写" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
                </div>
            </div>
INPUTHTML;
    }

    public function getHtml4Bootstrap () {
        return <<< INPUTHTML
            <div class="form-group">
                <span class="col-sm-3">{$this->getQuestionContentNoPos()}</span>
                <div class="col-sm-9">
                   <input type="text" readonly class="form-control datepicker" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
                </div>
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
           <input type="text" class="answer-box datectr" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
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
}

// 年-月
class YmQuestionCtr extends DateQuestionCtr
{
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
           <input type="text" class="answer-box datectr" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
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
}

// 年
class YQuestionCtr extends DateQuestionCtr
{
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
           <input type="text" class="answer-box datectr" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
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
}

// 月-日
class MdQuestionCtr extends DateQuestionCtr
{
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
           <input type="text" class="answer-box datectr" name='{$this->getContentInputName()}' value="{$this->getAnswerContent()}"/> {$this->getUnitHtml()}
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
}
