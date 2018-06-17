<?php

class MultTextBaseQuestionCtr extends TextBaseQuestionCtr
{

    // 重载
    public function getQaHtmlAnswerContent () {

        $str = '';

        for ($i = 1; $i <= 5; $i ++) {

            $text1 = "text{$i}1";
            $text2 = "text{$i}2";

            if ($i == 1) {
                $content = "content";
            } else {
                $content = "content{$i}";
            }

            $text1str = $this->xanswer->$text1;
            $answerContent = $this->xanswer->$content;
            $text2str = $this->xanswer->$text2;

            if (empty($text1str) && empty($answerContent) && empty($text2str)) {
                continue;
            }

            $str .= "$text1str $answerContent $text2str <br/> ";
        }

        return $str;
    }

    // TODO by sjp 重复代码
    public function getAnswerContents () {

        $arr = array();

        for ($i = 1; $i <= 5; $i ++) {

            $text1 = "text{$i}1";
            $text2 = "text{$i}2";

            if ($i == 1) {
                $content = "content";
            } else {
                $content = "content{$i}";
            }

            $text1str = $this->xanswer->$text1;
            $answerContent = $this->xanswer->$content;
            $text2str = $this->xanswer->$text2;

            if (empty($text1str) && empty($answerContent) && empty($text2str)) {
                continue;
            }

            $arr[$i] = "$answerContent";
        }

        return $arr;

    }

}
