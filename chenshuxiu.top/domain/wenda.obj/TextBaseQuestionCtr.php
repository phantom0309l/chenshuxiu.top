<?php

class TextBaseQuestionCtr extends QuestionCtr
{
    protected $bootstrapInputClass = '';
    // input区, 需要重载
    protected function getInputHtml()
    {
        return '子类必须覆写';
    }

    // 重载
    public function getHtml()
    {
        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            <div style="margin-top:5px;line-height:150%">
                {$this->getInputHtml()}
            </div>
INPUTHTML;
    }

    public function getHtmlOfCheckupTpl4Admin()
    {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }
        $str1 = '<div class="input-group"><span class="input-group-addon">';
        $str2 = '</span></div>';
        $str3 = '</span>';
        $str4 = '<span class="input-group-addon">';
        $this->bootstrapInputClass = 'form-control';
        return <<< INPUTHTML
            <div class="form-group {$inputClassDiv}">
                <label class="control-label col-md-2 col-sm-2">{$this->getQuestionContent()}</label>
                <div class="col-md-10 col-sm-10">
                    {$this->getStartHtml()}
                    {$this->getInputHtml($str1, $str2, $str3, $str4)}
                </div>
            </div>
INPUTHTML;
    }

    // 重载
    public function getWenzhenHtml()
    {
        return <<< INPUTHTML
            <div class="sheet-item">
                <p class="sheet-item-title">{$this->getQuestionContent()} {$this->getStartHtml()}</p>
                <div class="mt5">
                    {$this->getInputHtml()}
                </div>
            </div>
INPUTHTML;
    }

    // 重载
    public function getHtml4hwk()
    {
        return '子类必须重载';
    }

    // 生成单个input输入框,包括前后的文字,$no 为第几个输入框
    protected function getOneInputHtml($no = 1, $str1 = '<div>', $str2 = '</div>', $str3='', $str4='')
    {
        $unitHtml = '';
        // 传 0 时,为取多文本问题的第一个输入框
        if ($no == 0) {
            $ctype = "ctype1";
            $no = 1;
        } elseif ($no == 1) {
            $ctype = 'type';
            $unitHtml = $this->getUnitHtml();
            $qualitativeHtml = $this->getQualitativeHtml();
        } else {
            $ctype = "ctype{$no}";
        }

        $text1 = "text{$no}1";
        $text2 = "text{$no}2";
        $text1 = $this->xquestion->$text1;
        $text2 = $this->xquestion->$text2;
        $ctype = $this->xquestion->$ctype;
        $contentInputName = $this->getContentInputName($no);
        $answerContent = $this->getAnswerContent($no);
        $inputClass = $this->getInputClass($ctype);
        $inputType = $this->getInputType($ctype);

        $inputDataValue = $this->getInputDataValue($no);

        //起始日期存放在content2中
        $inputStartDateValue = $this->getInputDataValue(2);

        static $dateTypeArr = array('y','ym', 'ymd', 'md', 'hi');
        if (in_array(strtolower($ctype), $dateTypeArr)) {
            $inputClass .= ' calendar datepicker';
        }
        $inputClass .= ' ' . $this->bootstrapInputClass;

        if ($text1 && $text2) {
        } elseif ($text1 && !$text2) {
            $str4 = '';
            $str2 = '</div>';
        } elseif (!$text1 && $text2) {
            $str1 = '<div>';
            $str3 = '';
        } elseif (!$text1 && !$text2) {
            $str1 = '<div>';
            $str2 = '</div>';
            $str3 = '';
            $str4 = '';
        }
        //额外修补一次input-group

        return <<< INPUTHTML
            {$str1}
                {$text1}{$str3}
                <input type="{$inputType}" class="sheet-question sheet-input {$inputClass}" name='{$contentInputName}' data-startdate='{$inputStartDateValue}' data-value='{$inputDataValue}' value='{$answerContent}'/>
                {$unitHtml}
                {$qualitativeHtml}
                {$str4}{$text2}
            {$str2}
INPUTHTML;
    }
    protected function getOneInputHtml4CheckupTpl($no = 1)
    {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $unitHtml = '';
        // 传 0 时,为取多文本问题的第一个输入框
        if ($no == 0) {
            $ctype = "ctype1";
            $no = 1;
        } elseif ($no == 1) {
            $ctype = 'type';
            $unitHtml = $this->getUnitHtml();
        } else {
            $ctype = "ctype{$no}";
        }

        $text1 = "text{$no}1";
        $text2 = "text{$no}2";
        $text1 = $this->xquestion->$text1;
        $text2 = $this->xquestion->$text2;
        $ctype = $this->xquestion->$ctype;
        $contentInputName = $this->getContentInputName($no);
        $answerContent = $this->getAnswerContent($no);
        $inputClass = $this->getInputClass($ctype);
        $inputType = $this->getInputType($ctype);

        $inputDataValue = $this->getInputDataValue($no);

        static $dateTypeArr = array('y','ym', 'ymd', 'md');
        if (in_array(strtolower($ctype), $dateTypeArr)) {
            $inputClass .= ' datectr';
        }
        $inputClass .= ' ' . $this->bootstrapInputClass;

        return <<< INPUTHTML
            <span class="question-title">
                {$text1}
                <input type="{$inputType}" class="answer-box {$inputClass} {$inputClassDiv}" name='{$contentInputName}' data-value='{$inputDataValue}' value='{$answerContent}'/>
                {$unitHtml}
                {$text2}
            </span><br/>
INPUTHTML;
    }
}
