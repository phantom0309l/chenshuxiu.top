<?php

class MultChoiceQuestionCtr extends ChoiceBaseQuestionCtr
{

    public function getHtml () {
        return $this->getCheckboxHtml();
    }

    // 获取选中的optionids
    protected function getTheXOptionIdsOfAnswer () {
        if ($this->hasXAnswer()) {
            $xoptions = $this->xanswer->getTheXOptions();

            $arr = array();
            foreach ($xoptions as $a) {
                $arr[] = $a->id;
            }

            return $arr;
        }

        return $this->xquestion->getDefaultCheckedOptionIds();
    }

    public function getWenzhenHtml () {
        return $this->getCheckboxHtml4wenzhen();
    }

    public function getHtml4hwk () {
        return $this->getCheckboxHtml4hwk();
    }

    protected function getCheckboxHtml () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $fixs = $this->getXOptionFixArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer(), '<br/>', 'sheet-question-checkbox', $fixs);

        return <<< INPUTHTML
            <span class="sheet-question-content">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            <br/>
            $checkBoxStr
INPUTHTML;
    }

    protected function getCheckboxHtml4hwk () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer(), '<br/>', 'sheet-question-checkbox');

        $str = '<p class="mb5">';
        $str .= "{$this->getQuestionContent()}</p><br/>";
        $str .= $checkBoxStr;

        return $str;
    }

    protected function getCheckboxHtml4wenzhen ($otherstr = "") {

        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getWenzhenCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer());

        return <<< INPUTHTML
            <div class="sheet-item MultChoice" data-minvalue="{$this->xquestion->minvalue}" data-maxvalue="{$this->xquestion->maxvalue}">
                <p class="sheet-item-title">{$this->getQuestionContent()} {$this->getStartHtml()}</p>
                <div class="mt5 clearfix">
                    <div class="checkBox">
                        {$checkBoxStr}
                        {$otherstr}
                    </div>
                </div>
            </div>
INPUTHTML;
    }

    public function getHtmlOfCheckupTpl4Admin ($horizontal = true) {
        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer(), '', 'sheet-question-checkbox', [], '<label class="checkbox-inline">', '</label>');

        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        return <<< INPUTHTML
            <div class="form-group {$inputClassDiv}">
            <label class="sheet-question-content control-label col-md-2 col-sm-2">{$this->getQuestionContent()} {$this->getStartHtml()}</label>
            <div class="col-md-10 col-sm-10">
                {$checkBoxStr}
            </div>
            </div>
INPUTHTML;
    }

    // 具体实现
    public function getQaHtmlAnswerContent () {

        $arr = $this->getXOptionArray4HtmlCtr();
        $optionids = $this->getTheXOptionIdsOfAnswer();

        $str = '选择: ';
        foreach ($optionids as $optionid) {
            $str .= " {$arr [$optionid]}";
        }

        $otherContent = $this->getAnswerContent();
        if ($otherContent) {
            $str .= " [ {$otherContent} ]";
        }

        return $str;
    }

    // TODO by sjp 重复代码
    public function getAnswerContents () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $optionids = $this->getTheXOptionIdsOfAnswer();

        $str = "";
        foreach ($optionids as $optionid) {
            $str .= " {$arr [$optionid]}";
        }

        $otherContent = $this->getAnswerContent();
        if ($otherContent) {
            $str .= " [ {$otherContent} ]";
        }

        return array(
            $str);
    }

    public function getHtml4CheckupTpl () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $checkBoxStr = HtmlCtr::getCheckupTplCheckboxCtrImp($arr, $this->getContentInputName(), $this->getTheXOptionIdsOfAnswer());

        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        return <<< INPUTHTML
            <div style="margin-bottom:20px; " class="{$inputClassDiv}">
                <div class="triangle-blue"></div>
                <span class="question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
                <div style="margin-bottom:20px;margin-left:20px;width:80%; ">
                    <div class="checkBox">
                        {$checkBoxStr}
                    </div>
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

    public function getHtml4CheckupTplTableValue () {
        return $this->getAnswerContent();
    }

    //给脚本用的
    public function getHtml4CheckupTplTable_fixdb () {
        $arr = $this->getXOptionArray4HtmlCtr();
        return HtmlCtr::getCheckboxCtrTableImp($arr, $this->getTheXOptionIdsOfAnswer());
    }

    public function getAnswerContentNoStyle () {
        $arr = $this->getXOptionArray4HtmlCtr();
        return HtmlCtr::getCheckboxCtrTableImp($arr, $this->getTheXOptionIdsOfAnswer());
    }

}
