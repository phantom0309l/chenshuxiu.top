<?php

if (! defined("__UTILS_PHP")) {
    define("__UTILS_PHP", true);

    /*
     * 按照规则解析cookie字符串，获取cookie数组
     */
    function splitcookie ($cookievalue) {
        $array_cookie = array();
        /*
         * 格式：key1:val1/key2:val2/key3:val3
         */
        $list = explode("/", $cookievalue);
        foreach ($list as $str) {
            $tmp = explode(':', $str);
            $key = $tmp[0];
            $val = $tmp[1];
            $array_cookie[$key] = $val;
        }

        return $array_cookie;
    }

    /*
     * 按照联盟规则解析信息字符串，返回数组
     */
    function splitUnionStr ($str) {
        return splitcookie($str);
    }

    /*
     * 清除cookie
     */
    function clearcookie ($cookiename) {
        $expire = time() - 1 * 60 * 60;
        if (setcookie($cookiename, "", $expire)) {
            return true;
        }

        return false;
    }

    /*
     * 选定目录，然后在选定的目录下，创建以$filename命名的文件
     */
    function outputheader ($filename) {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        // Use the switch-generated Content-Type
        header("Content-Type: application/force-download");
        $header = 'Content-Disposition: attachment; filename=' . $filename . ';';
        header($header);
        header("Content-Transfer-Encoding: binary");
    }

    /*
     * 检索目录，返回该目录下文件名或文件夹的名称数组
     */
    function scan_dir ($dir) {
        $handle = opendir($dir);

        if (! $handle) {
            echo "opening directory failed!";
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $f[] = $file;
            }
        }

        closedir($handle);

        return $f;
    }

    /*
     * 扣税
     */
    function do_tax ($money) {
        if ($money <= 800) {
            return 0;
        } else
            if ($money > 800 && $money <= 4000) {
                $real = ($money - 800) * 0.2;
            } else
                if ($money > 4000 && $money <= 20000) {
                    $real = $money * 0.8 * 0.2;
                } else
                    if ($money > 20000 && $money <= 50000) {
                        $real = $money * 0.8 * 0.3 - 2000;
                    } else
                        if ($money > 50000) {
                            $real = $money * 0.8 * 0.4 - 7000;
                        }

        return $real;
    }

    function check_date ($ds = "0000-00-00") {
        if (! eregi("[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$", $ds)) {
            return false;
        }
        return true;
    }

    function check_datetime ($ds = "0000-00-00 00:00:00") {
        if (! eregi("[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$", $ds)) {
            return false;
        }
        return true;
    }

    function check_id_card ($ds = "") {
        if (! eregi("^[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9]$", $ds)) {
            return false;
        }
        return true;
    }

    function check_zipcode ($ds = "") {
        if (! eregi("[0-9]{6}", $ds)) {
            return false;
        }
        return true;
    }

    function check_qq ($ds = "") {
        if (! eregi("[0-9]+", $ds)) {
            return false;
        }
        return true;
    }

    function check ($ds = "") {
        if (eregi("\<\?", $ds) !== false || eregi("\<\%", $ds) || eregi("\<script", $ds)) {
            return false;
        }
        return true;
    }

    function check_phone ($ds = "") {
        if (! eregi("^[0-9][0-9\-]*[0-9]$", $ds)) {
            return false;
        }
        return true;
    }

    function check_email ($ds = "") {

        if (! eregi(
                "(^[a-zA-Z0-9][a-zA-Z0-9_\-\.]*[a-zA-Z0-9]+)(@[a-zA-Z0-9][a-zA-Z0-9_\-\.]*[a-zA-Z0-9]+)(\.cn$|\.com$|\.net$|\.name$|\.org$|\.com\.cn$|\.n et\.cn$|\.org\.cn$|\.gov\.cn$|\.info$|\.biz$|\.tv$|\.cc$)",
                $ds)) {
            return false;
        }
        return true;
    }

    function check_mobile ($ds = "") {
        if (! eregi("^[0-9][0-9\-]*[0-9]$", $ds)) {
            return false;
        }
        return true;
    }

    function check_domain ($domain_name = "") {
        if ($domain_name == null) {
            $domain_name = "";
        }

        if (! eregi(
                "(^[a-zA-Z0-9][a-zA-Z0-9_\-]*[a-zA-Z0-9]+)(\.cn$|\.com$|\.net$|\.name$|\.org
$|\.com\.cn$|\.n et\.cn$|\.org\.cn$|\.gov\.cn$|\.info$|\.biz$|\.tv$|\.cc$)", $domain_name)) {
            return false;
        }

        return true;
    }

    /*
     * $urlparse =
     * vap_url_parse("http://kkk.abs.www.wislook.com:8080/index.htm"); echo
     * $urlparse['protocol']."\n"; "http or https" echo
     * $urlparse['domain']."\n"; "wislook.com" echo $urlparse['host']."\n";
     * "kkk.abs.www.wislook.com" echo $urlparse['file']."\n"; "/index.htm" echo
     * $urlparse['port']."\n"; "8080"
     */

    function vap_url_parse ($url) {
        $sp = null;
        $sp2 = null;
        $len = null;
        $absurl = null;
        $urllen = null;
        $urlparse = null;

        if ($url == null) {
            return false;
        }

        if (strncasecmp($url, "http://", 7) == 0) {
            $urlparse['protocol'] = "http";
            $absurl = substr($url, 7);
        } else
            if (strncasecmp($url, "https://", 8) == 0) {
                $urlparse['protocol'] = "https";
                $absurl = substr($url, 8);
            } else {
                return false;
            }

        $urllen = strlen($absurl);

        if (($sp = strstr($absurl, ":")) == null) {

            if (($sp = strstr($absurl, "/")) == null) {
                $urlparse['host'] = $absurl;
                $urlparse['file'] = "/";
            } else {
                $len = strlen($absurl) - strlen($sp);
                $urlparse['host'] = substr($absurl, 0, $len);
                $urlparse['file'] = $sp;
            }

            $urlparse['port'] = "80";
        } else {
            $len = strlen($absurl) - strlen($sp);

            $urlparse['host'] = substr($absurl, 0, $len);

            if (($sp2 = strstr($sp, "/")) == null) {
                $urlparse['port'] = substr($sp, 1);
                $urlparse['file'] = "/";
            } else {
                $len = null;

                $len = strlen($sp) - strlen($sp2) - 1;

                $urlparse['port'] = substr($sp, 1, $len);

                if (strcmp($sp2, substr($absurl, $urllen - 1)) == 0) {

                    $urlparse['file'] = "/";
                } else {
                    $urlparse['file'] = $sp2;
                }
            }
        }

        if (! eregi(
                "([a-zA-Z0-9][a-zA-Z0-9_\-\.]*[a-zA-Z0-9]+\.)([a-zA-Z0-9][a-zA-Z0-9_\-]*[a-zA-Z0-9]+)(\.cn$|\.com$|\.net$|\.name$|\.org$|\.com\.cn$|\.com\.ru$|\.net\.cn$|\.org\.cn$|\.gov\.cn$|\.info$|\.biz$|\.tv$|\.cc$)",
                $urlparse['host'])) {
            return false;
        }

        if (! eregi(
                "\.([a-zA-Z0-9][a-zA-Z0-9_\-]*[a-zA-Z0-9]+)(\.cn$|\.com$|\.net$|\.name$|\.org$|\.com\.cn$|\.com\.ru$|\.net\.cn$|\.org\.cn$|\.gov\.cn$|\.info$|\.biz$|\.tv$|\.cc$)",
                $urlparse['host'], $ee)) {
            return false;
        }

        $urlparse['domain'] = substr($ee[0], 1);

        if (! eregi("^[0-9]+$", $urlparse['port'])) {
            return false;
        }

        return $urlparse;
    }

    /*
     * 检测虚拟主机
     */
    function check_host ($hostname = "") {
        if ($hostname == null) {
            $hostname = "";
        }

        return eregi(
                "(^[a-zA-Z0-9][a-zA-Z0-9_\-\.]*[a-zA-Z0-9]+)(\.cn$|\.com$|\.net$|\.name$|\.org
$|\.com\.cn$|\.n et\.cn$|\.org\.cn$|\.gov\.cn$|\.info$|\.biz$|\.tv$|\.cc$)", $hostname);
    }

    function vap_db_date ($tim = 0) {
        if ($tim == 0) {
            $tim = time();
        }
        return date("Y-m-d", $tim);
    }

    function vap_db_datetime ($tim = 0) {
        if ($tim == 0) {
            $tim = time();
        }
        return date("Y-m-d H:i:s", $tim);
    }

    function vap_bbs_date ($tim = 0) {
        if ($tim == 0) {
            $tim = time();
        }
        return date("m-d", $tim);
    }

    function vap_days_per_month ($year = null, $month = null) {
        if ($month == null || $year == null) {
            $month = date("m");
            $year = date("Y");
        }

        if ($month == 12) {
            $year += 1;
            $month = 1;
        } else {
            $month += 1;
        }

        $lastday = mktime(0, 0, 0, $month, 0, $year);

        return strftime("%d", $lastday);
    }

    function vap_error ($msg = null, $page = null) {
        if ($msg != null) {
            $msg = urlencode($msg);
        }
        if ($page != null) {
            $page = urlencode($page);
        }
        header("Location:/notice.htm?msg=$msg&page=$page");
        exit();
    }

    function vap_location ($page) {
        header("Location: $page");
        exit();
    }

    function vap_filter ($value, $defaultval = null) {
        if (get_magic_quotes_gpc())
            return isset($value) ? $value : $defaultval;
        return isset($value) ? mysql_escape_string($value) : mysql_escape_string($defaultval);
    }

    /*
     * today 今天 yesterday 昨天 last7days 前 7 天 thismonth 本月 lastmonth 上月 thisweek
     * 本周（周一-周日） lastweek 上星期（星期日至星期六） lastnatureweek		上个自然周(上周一-周日)
     * llnatureweek		上上个自然周(上上周一-周日) lastbusinessweek 上个工作周（星期一至星期五） alltime
     * 所有时间
     */
    function trans_date (&$frmDate, &$toDate, $simpleType) {
        $type = array(
            "today",
            "yesterday",
            "qiantian",
            "last7days",
            "thismonth",
            "lastmonth",
            "llmonth",
            "thisweek",
            "lastweek",
            "lastnatureweek",
            "llnatureweek",
            "lastbusinessweek",
            "alltime");

        if (! in_array($simpleType, $type))
            return false;

        if ($simpleType == "today") {
            $frmDate = date("Y-m-d");
            $toDate = date("Y-m-d");
        }

        if ($simpleType == "yesterday") {
            $today = date("d");

            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
            ;
        }

        if ($simpleType == "qiantian") {
            $today = date("d");

            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
            ;
        }

        if ($simpleType == "last7days") {
            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            ;
        }

        if ($simpleType == "thismonth") {
            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            ;
        }
        if ($simpleType == "lastmonth") {
            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
        }
        if ($simpleType == "llmonth") {
            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 2, 1, date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, 0, date("Y")));
        }
        if ($simpleType == "thisweek") {
            $w = date("w");

            $frmDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $w, date("Y"), date("Y")));
            $toDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 6 - $w, date("Y")));
        }

        if ($simpleType == "lastweek") {
            $w = date("w", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $y = date("Y", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $m = date("m", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $d = date("d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));

            $frmDate = date("Y-m-d", mktime(0, 0, 0, $m, $d - $w, $y));
            $toDate = date("Y-m-d", mktime(0, 0, 0, $m, $d + 6 - $w, $y));
        }

        if ($simpleType == "lastnatureweek") {
            $w = date("w", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $y = date("Y", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $m = date("m", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $d = date("d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));

            $frmDate = date("Y-m-d", mktime(0, 0, 0, $m, $d - $w + 1, $y));
            $toDate = date("Y-m-d", mktime(0, 0, 0, $m, $d + 7 - $w, $y));
        }

        if ($simpleType == "llnatureweek") {
            $w = date("w", mktime(0, 0, 0, date("m"), date("d") - 14, date("Y")));
            $y = date("Y", mktime(0, 0, 0, date("m"), date("d") - 14, date("Y")));
            $m = date("m", mktime(0, 0, 0, date("m"), date("d") - 14, date("Y")));
            $d = date("d", mktime(0, 0, 0, date("m"), date("d") - 14, date("Y")));

            $frmDate = date("Y-m-d", mktime(0, 0, 0, $m, $d - $w + 1, $y));
            $toDate = date("Y-m-d", mktime(0, 0, 0, $m, $d + 7 - $w, $y));
        }

        if ($simpleType == "lastbusinessweek") {
            $w = date("w", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $y = date("Y", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $m = date("m", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $d = date("d", mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));

            $frmDate = date("Y-m-d", mktime(0, 0, 0, $m, $d - $w + 1, $y));
            $toDate = date("Y-m-d", mktime(0, 0, 0, $m, $d + 5 - $w, $y));
        }

        if ($simpleType == "alltime") {
            $frmDate = date("2005-01-01");
            $toDate = date("Y-m-d");
        }
        return true;
    }

    function checkdatevalue (&$year, &$month, &$day) {
        if (! isset($year))
            return false;
        if (! isset($month))
            return false;
        if (! isset($day))
            return false;

        $month_30 = array(
            4,
            6,
            9,
            11);

        if (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)) {
            if ($month == 2) {
                if ($day > 29) {
                    $day = 29;
                }
            } elseif (in_array($month, $month_30)) {
                if ($day > 30) {
                    $day = 30;
                }
            }
        } else {
            if ($month == 2) {
                if ($day > 28) {
                    $day = 28;
                }
            } elseif (in_array($month, $month_30)) {
                if ($day > 30) {
                    $day = 30;
                }
            }
        }

        // $redate = date("Y-m-d",mktime(0,0,0,$year,$month,$day));

        return true;
    }

    function between_date ($date_star, $date_end) {
        if (! isset($date_star))
            return false;
        if (! isset($date_end))
            return false;

        $star = strtotime($date_star);
        $end = strtotime($date_end);
        $days = ($end - $star) / (60 * 60 * 24);
        return $days;
    }

    function arr2weekput ($arrweek) {
        if (! isset($arrweek) || ! is_array($arrweek) || count($arrweek) > 7)
            return "1111111";

        $str = "";
        for ($i = 1; $i <= 7; $i ++) {
            if (in_array($i, $arrweek))
                $str .= '1';
            else
                $str .= '0';
        }
        return $str;
    }

    function arr2hourput ($arrhour) {
        if (! isset($arrhour) || ! is_array($arrhour) || count($arrhour) > 24)
            return "111111111111111111111111";

        $str = "";
        for ($i = 0; $i < 24; $i ++) {
            if (in_array($i, $arrhour))
                $str .= '1';
            else
                $str .= '0';
        }
        return $str;
    }

    function getAuditStatusName ($audit_status) {
        if ($audit_status == VAP_AUDIT_STATUS_NEW)
            return "新提交";
        if ($audit_status == VAP_AUDIT_STATUS_PROCESSING)
            return "审核中";
        if ($audit_status == VAP_AUDIT_STATUS_CONSULT)
            return "协商中";
        if ($audit_status == VAP_AUDIT_STATUS_PASSED)
            return "审核通过";
        if ($audit_status == VAP_AUDIT_STATUS_REJECT)
            return "拒绝";
        return "";
    }

    function is_valid_datetime ($date) {
        return true;
    }

    function is_valid_date ($date) {
        echo "shixulianglslsllslslslls";
        if (! eregi("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $date))
            return false;

        list ($year, $month, $day) = explode("-", $date);

        return (checkdate($month, $day, $year));
    }

    function is_valid_weekput ($str) {
        return (eregi("^[0|1]{7}$", $str));
    }

    function is_valid_hourput ($str) {
        return (eregi("^[0|1]{24}$", $str));
    }

    function mysubstr ($content, $length) {
        if (strlen($content) > $length) {
            $num = 0;
            for ($i = 0; $i < $length - 3; $i ++) {
                if (ord($content[$i]) > 0xa0)
                    $num ++;
            }
            $num % 2 == 1 ? $content = substr($content, 0, $length - 4) : $content = substr($content, 0, $length - 3);
            $content .= '';
        }
        return $content;
    }

} /*
   * __UTILS_PHP
   */
?>