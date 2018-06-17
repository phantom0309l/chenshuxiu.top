<?php

class FiveTextQuestionCtr extends MultTextBaseQuestionCtr
{

    protected function getInputHtml ($str1='<div>', $str2 = '</div>', $str3 = '', $str4 = '') {
        $str = $this->getOneInputHtml(0, $str1, $str2, $str3, $str4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml(2, $str1, $str2, $str3, $str4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml(3, $str1, $str2, $str3, $str4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml(4, $str1, $str2, $str3, $str4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml(5, $str1, $str2, $str3, $str4);
        return $str;
    }

    public function getHtml4CheckupTpl () {
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }

        $str = $this->getOneInputHtml4CheckupTpl(0);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml4CheckupTpl(2);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml4CheckupTpl(3);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml4CheckupTpl(4);
        $str .= "\n<br/>";
        $str .= $this->getOneInputHtml4CheckupTpl(5);

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
