<?php

class ChoiceBaseQuestionCtr extends QuestionCtr
{

    public function getHtml () {
        return '需要覆写';
    }

    // 获取需要的option数组
    protected function getXOptionArray4HtmlCtr () {
        $xoptions = $this->xquestion->getOptions();
        return XOption::toArray4HtmlCtr($xoptions);
    }

    // 获取需要的optionFix数组,有显隐的属性
    protected function getXOptionFixArray4HtmlCtr () {
        $xoptions = $this->xquestion->getOptions();
        return XOption::toFixArray4HtmlCtr($xoptions);
    }

    protected function getXOptionFixArray4HtmlCtrCheckupTpl () {
        $xoptions = $this->xquestion->getOptions();
        return XOption::toFixArray4HtmlCtrCheckupTpl($xoptions);
    }
}
