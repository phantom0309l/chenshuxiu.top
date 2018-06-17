<?php

class TwoTextQuestionCtr extends MultTextBaseQuestionCtr
{

    // 重载
    protected function getInputHtml ($str1='<div>', $str2 = '</div>', $str3 = '', $str4 = '') {
        $str = '';
        $str = $this->getOneInputHtml(0, $str1, $str2, $str3, $str4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml(2, $str1, $str2, $str3, $str4);
        return $str;
    }

    // 重载,考虑到量表问卷两个问题,多是高压低压问题,所以两个输入框之间不加换行
    public function getScaleHtml () {
        $str1 = $this->getOneInputHtml(0, '', '');
        $str2 = $this->getOneInputHtml(2, '', '');
        return <<< INPUTHTML
            <div class="sheet-item">
                <p class="sheet-item-title">{$this->getQuestionContent()} {$this->getStartHtml()}</p>
                <div class="mt5">
                    <div>
                        {$str1}
                    </div>
                    <div class="mt5">
                        {$str2}
                    </div>
                </div>
            </div>
INPUTHTML;
    }

    // 重载,考虑到量表问卷两个问题,多是高压低压问题,所以两个输入框之间不加换行
    public function getHtml4hwk () {
        $str1 = $this->getOneInputHtml(0, '', '');
        $str2 = $this->getOneInputHtml(2, '', '');
        return <<< INPUTHTML
            <p class="mb5">
                {$this->getQuestionContent()} <small class='qcmnt'>{$this->getQuestionTip()}</small>
            </p>
            <div style="margin-top:5px;">
                {$str1}
                {$str2}
            </div>
INPUTHTML;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $str = $this->getOneInputHtml4CheckupTpl(0);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml4CheckupTpl(2);

        return <<< INPUTHTML
        <div style="margin-bottom:20px;position:relative; " class="{$inputClassDiv}">
           <div class="triangle-blue" style='position:absolute;left:0px;top:10px;'></div>
           <div style="display:inline-block;margin-left:10px;">
              {$str}
           </div>
        </div>
        <div style="clear:both;"></div>
INPUTHTML;
    }

}
