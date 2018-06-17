<?php

// 段落
class SectionQuestionCtr extends QuestionCtr
{

    // 重载
    public function getHtml () {
        return <<< INPUTHTML
            <span class="sheet-question-section">{$this->getQuestionContent()}</span>
            <input type="hidden" name='{$this->getContentInputName()}' value='{$this->getQuestionContentNoPos()}'/>
INPUTHTML;
    }

    // 重载
    public function getHtmlOfCheckupTpl4Admin () {
        return <<< INPUTHTML
            <div class="form-group">
               <label class="control-label col-md-2 col-sm-2"></label>
               <div class="col-md-10 col-sm-10">
                    <div class="well well-sm" style="margin-bottom:0;">
                        <span class="sheet-question-section">{$this->getQuestionContent()}</span>
                        <input type="hidden" name='{$this->getContentInputName()}' value='{$this->getQuestionContentNoPos()}'/>
                    </div>
               </div>
            </div>
INPUTHTML;
    }

    public function getQaHtml4lesson () {
        $qcontentPre = '<p class="mainp_hwk">';
        $qcontentSuf = '</p>';
        $answerContentPre = '';
        $answerContentSuf = '';
        return $this->getQaHtml($qcontentPre, $qcontentSuf, $answerContentPre, $answerContentSuf);
    }

    // 重载
    public function getQaHtmlAnswerContent () {
        return '';
    }

    public function getHtml4CheckupTpl () {
        return <<< INPUTHTML
            <div style="margin-bottom:20px; ">
                <div class="triangle-blue"></div>
                <span class="questionpart question-title">
                    {$this->getQuestionContentNoPos()}
                </span>
            </div>
            <div style="clear:both;"></div>
INPUTHTML;
    }

    public function getHtml4CheckupTplTable () {
        return '';
    }
}
