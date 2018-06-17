<?php

class FUtil
{

    // 将实体数组转换成json数组
    public static function entitysToJsonArray (array $entitys, $toJsonFunc = 'toJsonArray') {
        $arr = array();

        foreach ($entitys as $a) {
            if ($a instanceof Pipe) {

                $object = $a->obj;
                if ($object == null) {
                    continue;
                }

                if ($object instanceof PushMsg && $object->sendway == 'wechat_template') {
                    continue;
                }

                $array = $a->$toJsonFunc();

                if (! empty($array)) {
                    $arr[] = $array;
                }
            } elseif ($a instanceof Entity) {
                $arr[] = $a->$toJsonFunc();
            }
        }
        return $arr;
    }

    public static function json_walker ($obj_arr) {
        $arr_json = "null";
        if (count($obj_arr) > 0) {
            $arr_json = json_encode(array_map(function  ($x) {
                return $x->toArray();
            }, $obj_arr));
        }
        return $arr_json;
    }

    // 将实体数组转换成json数组
    public static function entitysToIdsStr (array $entitys) {
        $arr = array();
        foreach ($entitys as $a) {
            if ($a instanceof Patient) {
                $arr[] = $a->id;
            }
        }

        return implode(',', $arr);
    }

    public static function safeGuardNtimes ($proc, $n) {
        $limit = $proc();
        $n --;
        while ((! $limit) and $n > 0) {
            $limit = $proc();
            $n --;
        }
    }

    // 计算数组内满足某条件成员数量
    // 第二个参数务必为包含条件的函数，返回真假用以达成计数条件
    // TODO 暂不支持多参条件与卷积复查
    public static function countEx (array $arr, $proc) {
        $num = 0;
        foreach ($arr as $x) {
            if ($proc($x)) {
                $num += 1;
            } else {
                continue;
            }
        }
        return $num;
    }

    public static function pagebar ($pagenum, $pagecnt) {
        $leftside = (($tmp = $pagenum - 6) < 1) ? 1 : $tmp;
        $rightside = (($tmp = $leftside + 12) > $pagecnt) ? $pagecnt : $tmp;
        return range($leftside, $rightside);
    }

    // 将xml字符串解析为对象
    public static function bodyXMLToArray () {
        $xml = self::bodyXMLToObj();
        return self::xmlobj2array($xml);
    }

    // 对象转数组
    public static function xmlobj2array ($xml) {
        return json_decode(json_encode($xml), true);
    }

    // 将xml字符串解析为对象
    public static function bodyXMLToObj () {
        $xmlstr = self::getBodyXML();
        if ($xmlstr == null) {
            return null;
        }

        $xmlobj = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $xmlobj;
    }

    // getBodyXML
    public static function getBodyXML () {
        $xmlstr = file_get_contents('php://input');

        // 若请求body为空则直接返回, 不再做更多计算
        if (empty($xmlstr)) {
            Debug::info(" php://input = null ");
            return null;
        } else {
            Debug::trace(" php://input = [[[ " . $xmlstr . " ]]] ");
            return $xmlstr;
        }
    }

    public static function getSecuritySqlStr ($inputArr, $whitelist, $splitstr) {
        $str = "";
        $temp = array();
        foreach ($inputArr as $a) {
            if (in_array($a, $whitelist)) {
                $temp[] = $a;
            }
        }

        if (count($temp) > 0) {
            $str = implode($splitstr, $temp);
        }
        return $str;
    }

    public static function getYmdBetween2Date ($date1, $date2) {
        $time1 = strtotime($date1);
        $_time1 = $time1;
        $time2 = strtotime($date2);
        $months = 0;
        while (($time1 = strtotime('+1 MONTH', $time1)) <= $time2) {
            $months ++;
        }
        $ret = '';
        if ($months == 0) {
            $days = ($time2 - $_time1) / (60 * 60 * 24);
            $ret = $days . '天';
        } else
            if ($months == 12) {
                $ret = '1年';
            } else
                if ($months > 12) {
                    $_month = $months % 12;
                    $ret = floor($months / 12) . '年';
                    if ($_month > 0) {
                        $ret .= $_month . '月';
                    }
                } else {
                    $ret = $months . '个月';
                }
        return $ret;
    }

    public static function getRandStr ($len) {
        $chars_array = array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "a",
            "b",
            "c",
            "d",
            "e",
            "f",
            "g",
            "h",
            "i",
            "j",
            "k",
            "l",
            "m",
            "n",
            "o",
            "p",
            "q",
            "r",
            "s",
            "t",
            "u",
            "v",
            "w",
            "x",
            "y",
            "z",
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z");
        $charsLen = count($chars_array) - 1;

        $outputstr = "";
        for ($i = 0; $i < $len; $i ++) {
            $outputstr .= $chars_array[mt_rand(0, $charsLen)];
        }
        return $outputstr;
    }

    public static function isHttps () {
        if ($_SERVER['SERVER_PORT'] == 443) {
            return TRUE;
        }
        return false;
    }

    public static function curlGet ($url, $params = array(), $timeout = 2) {
        $ch = curl_init();
        if ($params) {
            $queryString = http_build_query($params);
            if (strpos($url, '?') !== false) {
                $url .= '&' . $queryString;
            } else {
                $url .= '?' . $queryString;
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 超时为2秒
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            Debug::warn('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return $res;
    }

    public static function curlPost ($url, $data = [], $timeout = 2, $headers = []) {
        $ch = curl_init();
        // print_r($ch);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 超时为2秒
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            Debug::warn('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $res;
    }

    // 法律规定的放假日期, 及公司规定的放假日期
    private static $law_holidays = array(
        "2018-01-01",
        "2018-02-14",
        "2018-02-15",
        "2018-02-16",
        "2018-02-17",
        "2018-02-18",
        "2018-02-19",
        "2018-02-20",
        "2018-02-21",
        "2018-04-05",
        "2018-04-06",
        "2018-04-07",
        "2018-04-29",
        "2018-04-30",
        "2018-05-01",
        "2018-06-18",
        "2018-09-24",
        "2018-10-01",
        "2018-10-02",
        "2018-10-03",
        "2018-10-04",
        "2018-10-05",
        "2018-10-06",
        "2018-10-07",
    );

    // 由于放假需要额外工作的周六日
    private static $extra_workdays = array(
        "2018-02-11",
        "2018-02-24",
        "2018-04-08",
        "2018-04-28",
        "2018-09-29",
        "2018-09-30",
    );

    public static function isHoliday ($date = "") {
        if ($date == "") {
            $date = date("Y-m-d");
        }
        $law_holidays = self::$law_holidays;
        $extra_workdays = self::$extra_workdays;

        // 如果是法定节假日直接返回
        if (in_array($date, $law_holidays)) {
            return true;
        }

        // 判断周六日是不是补班
        $w = date("w", strtotime($date));
        if ($w == 0 || $w == 6) {
            if (in_array($date, $extra_workdays)) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    // 是否是法定放假日
    public static function isFaDingHoliday ($date) {
        if ($date == "") {
            $date = date("Y-m-d");
        }
        $law_holidays = self::$law_holidays;

        // 如果是法定节假日直接返回
        if (in_array($date, $law_holidays)) {
            return true;
        } else {
            return false;
        }
    }

    // 获取方寸工作日
    public static function getWorkDate ($date = '') {
        if ($date == "") {
            $date = date("Y-m-d");
        }

        $law_holidays = self::$law_holidays;
        while (in_array($date, $law_holidays)) {
            $date = date('Y-m-d', strtotime($date) + 3600 * 24);
        }

        return $date;
    }

    public static function ip2Region (string $ip) {
        if (! trim($ip)) {
            return null;
        }

        $ip2region_host = Config::getConfig("ip2region_host");
        $params = [
            "ip" => $ip];

        $ret = self::curlGet($ip2region_host, $params);
        if (! $ret) {
            Debug::warn(__METHOD__ . " ret is empty ip is {$ip}");
            return null;
        }

        $arr = json_decode($ret, true);
        if (isset($arr['error'])) {
            Debug::warn(__METHOD__ . " {$arr['error']} ip is {$ip}");
            return null;
        }

        if ($arr['CityId'] == 0) {
            Debug::warn(__METHOD__ . " {$arr['Country']} ip is {$ip}");
            return null;
        }

        $arr['address'] = $arr['Province'] . $arr['City'] . $arr['ISP'];

        return $arr;
    }

    // return array
    public static function getImageSize ($url) {
        if (empty($url)) {
            return [];
        }
        $ch = curl_init($url);
        // 超时设置
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        // 取前面 168 个字符 通过四张测试图读取宽高结果都没有问题,若获取不到数据可适当加大数值
        curl_setopt($ch, CURLOPT_RANGE, '0-255');
        // 跟踪301跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // 返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $dataBlock = curl_exec($ch);

        curl_close($ch);

        if (! $dataBlock)
            return [];

            // 将读取的图片信息转化为图片路径并获取图片信息,经测试,这里的转化设置 jpeg 对获取png,gif的信息没有影响,无须分别设置
            // 有些图片虽然可以在浏览器查看但实际已被损坏可能无法解析信息
        $size = getimagesize('data://image/jpeg;base64,' . base64_encode($dataBlock));
        if (empty($size)) {
            return [];
        }

        $result = [];
        $result['width'] = $size[0];
        $result['height'] = $size[1];

        return $result;
    }
    //作者:chenning
    //过滤字符串内的ascii码小于等于31或者等于127的不可见字符.
    public static function filterInvisibleCharOfStr($str) {
        $strResult = '';
        $length = mb_strlen($str);
        for( $i = 0; $i < $length; $i++ ){
            $char = mb_substr($str, $i, 1, 'UTF-8');
            if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $char)) {
                $strResult .= $char;
            }else {
                if (ord($char) <= 31 || ord($char) == 127) {
                    //ascii码小于31 或者 等于127的字符 丢弃
                }else {
                    $strResult .= $char;
                }
            }
        }
        return $strResult;
    }
    //作者:chenning
    //过滤数组中每个值中的不可见字符
    public static function filterInvisibleChar($arr) {

        if (!is_array($arr)) {
            return self::filterInvisibleCharOfStr($arr);
        }else {
            $arrResult = [];
            foreach ($arr as $key => $value ) {
                $arrResult[$key] = self::filterInvisibleChar($value);
            }
            return $arrResult;
        }
    }

}
