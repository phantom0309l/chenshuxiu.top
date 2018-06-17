<?php

// $string = "渑池";
// $flow = MyPinYin::getPinyin($string);
// print_r($flow);

class MyPinYin
{

    private static $pinyin_table = null;

    public static function loadTable () {
        if (empty(self::$pinyin_table)) {
            include (dirname(__FILE__) . '/pinyin.php');
            self::$pinyin_table = $pinyin_table;
        }

        return self::$pinyin_table;
    }
    // 单个拼音: mianchi
    public static function getPinyin ($string) {
        $pinyins = self::getPinyins($string);
        if (empty($pinyins)) {
            return "";
        }

        $pinyin = $pinyins[0];

        return str_replace(" ", "", $pinyin);
    }

    // 数组 每项： mian chi
    public static function getPinyins ($string) {
        $flows = self::get_pinyin_array($string);
        if (empty($flows)) {
            return array();
        }

        $pinyins = array();
        foreach ($flows as $a) {
            $pinyin = str_replace("_", "", str_replace("__", " ", $a));
            $pinyin = mb_convert_encoding($pinyin, "utf-8", "gbk");
            $pinyins[] = $pinyin;
        }
        return $pinyins;
    }

    // 数组 每项： _mian__chi_
    public static function get_pinyin_array ($string) {
        // TODO by sjp 20100426 源程序是gbk编码，所以需要转换
        $string = mb_convert_encoding($string, "gbk", "utf-8");

        $pinyin_table = self::loadTable();

        $flow = array();
        for ($i = 0; $i < strlen($string); $i ++) {
            // echo "---";
            // echo ord($string[$i]);
            // echo "---";
            if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe) {
                $h = ord($string[$i]);
                if (isset($string[$i + 1])) {
                    $i ++;
                    $l = ord($string[$i]);
                    if (isset($pinyin_table[$h][$l])) {
                        array_push($flow, $pinyin_table[$h][$l]);
                    } else {
                        array_push($flow, $h);
                        array_push($flow, $l);
                    }
                } else {
                    array_push($flow, ord($string[$i]));
                }
            } else {
                array_push($flow, ord($string[$i]));
            }
        }

        // print_r($flow);

        $pinyin = array();
        $pinyin[0] = '';
        for ($i = 0; $i < sizeof($flow); $i ++) {
            if (is_array($flow[$i])) {
                if (sizeof($flow[$i]) == 1) {
                    foreach ($pinyin as $key => $value) {
                        $pinyin[$key] .= "_" . $flow[$i][0] . "_";
                    }
                }
                if (sizeof($flow[$i]) > 1) {
                    $tmp1 = $pinyin;
                    foreach ($pinyin as $key => $value) {
                        $pinyin[$key] .= "_" . $flow[$i][0] . "_";
                    }
                    for ($j = 1; $j < sizeof($flow[$i]); $j ++) {
                        $tmp2 = $tmp1;
                        for ($k = 0; $k < sizeof($tmp2); $k ++) {
                            $tmp2[$k] .= "_" . $flow[$i][$j] . "_";
                        }
                        array_splice($pinyin, sizeof($pinyin), 0, $tmp2);
                    }
                }
            } else {
                foreach ($pinyin as $key => $value) {
                    $pinyin[$key] .= chr($flow[$i]);
                }
            }
        }
        return $pinyin;
    }
}