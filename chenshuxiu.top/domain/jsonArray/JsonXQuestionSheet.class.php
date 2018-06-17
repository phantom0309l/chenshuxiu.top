<?php

class JsonXQuestionSheet
{
    // jsonArrayForAdmin
    public static function jsonArrayForAdmin ($questionSheet, $issimple = 0) {
        $ret = array();

        if ($questionSheet instanceof XQuestionSheet) {
            $ret['questionsheet'] = $questionSheet->toJsonArray();
        } else {
            $ret['questionsheet'] = array();
            return $ret;
        }

        $questions = $questionSheet->getQuestions($issimple);

        foreach ($questions as $question) {
            $question->isdefaulthide = $question->isDefaultHide();
            $question->type2 = $question->type;
            if ($question->type2 == 'Section') {
                $question->type2 = 'FCSection';
            } elseif ($question->type == 'Text') {
                $question->type2 = 'FCText';
            } elseif ($question->type == 'TextArea') {
                $question->type2 = 'FCTextArea';
            } elseif ($question->type == 'Caption') {
                $question->type2 = 'FCCaption';
            }
            $questionArr = array();
            $questionArr = $question->toJsonArray();
            $options = $question->getOptions();
            $questionArr['options'] = FUtil::entitysToJsonArray($options);
            $ret['questions'][] = $questionArr;
        }
        return $ret;
    }
}