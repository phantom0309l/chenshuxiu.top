<?php

class XAnswerHelper
{

    public static function getFieldDesc ($field) {
        return isset(self::$fieldDescs[$field]) ? self::$fieldDescs[$field] : '';
    }

    public static function getFields () {
        return self::$fieldDescs;
    }

    private static $fieldDescs = array(
        'content' => '内容',
        'unit' => '单位',
        'qualitative' => '定性');
}
