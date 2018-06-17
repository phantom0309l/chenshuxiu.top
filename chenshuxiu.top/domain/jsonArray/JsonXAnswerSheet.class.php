<?php

class JsonXAnswerSheet
{
    // jsonArrayForAdmin 数组结构的答卷数据
    public static function jsonArrayForAdmin ($xanswersheet, $issimple = 0) {
        $data = array();

        if ($xanswersheet instanceof XAnswerSheet) {
            $data = $xanswersheet->toJsonArray();
            $answers = $xanswersheet->getAnswers($issimple);
            foreach ($answers as $answer) {
                $answer->xquestionname = $answer->xquestion->getTitle();
                $answer->xquestiontype = $answer->xquestion->type;
                $answerArr = $answer->toJsonArrayNew();
                $options = $answer->getTheXOptions();
                $optionArr = FUtil::entitysToJsonArray($options);
                $answerArr['options'] = $optionArr;
                $data['answers'][] = $answerArr;
            }
        }

        return $data;
    }
}