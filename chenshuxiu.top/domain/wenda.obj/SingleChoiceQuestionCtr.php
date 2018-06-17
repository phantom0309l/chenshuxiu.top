<?php

class SingleChoiceQuestionCtr extends ChoiceBaseQuestionCtr
{

    public function getHtml () {
        return '需要覆写';
    }

    // 获取选中的optionid
    protected function getTheXOptionIdOfAnswer () {
        if ($this->hasXAnswer()) {
            return $this->xanswer->getTheXOption()->id;
        }

        return $this->xquestion->getDefaultCheckedOptionId();
    }

    // 重载
    public function getQaHtmlAnswerContent () {

        $arr = $this->getXOptionArray4HtmlCtr();
        $optionid = $this->getTheXOptionIdOfAnswer();

        $str = "选择: ";
        $str .= $arr[$optionid];

        $otherContent = $this->getAnswerContent();
        if ($otherContent) {
            $str .= " [ {$otherContent} ]";
        }

        return $str;
    }

    // TODO by sjp 重复代码
    public function getAnswerContents () {
        $arr = $this->getXOptionArray4HtmlCtr();
        $optionid = $this->getTheXOptionIdOfAnswer();

        $str = "";
        $str .= $arr[$optionid];

        $otherContent = $this->getAnswerContent();
        if ($otherContent) {
            $str .= " [ {$otherContent} ]";
        }

        return array(
            $str);
    }
}
