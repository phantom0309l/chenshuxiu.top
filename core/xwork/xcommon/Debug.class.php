<?php

/**
 * Debug
 * @desc        Debug,记录日志
 * @remark        依赖类:    XUtility , Config , Noticer[其实只依赖Noticer接口概念]
 * @copyright    (c)2012 xwork.
 * @file        Debug.class.php
 * @author        shijianping <shijpcn@qq.com>
 * @date        2012-02-26
 */
class Debug
{

    // 是否开启debug
    public static $debug = "DevClose";

    // 是否显示debug信息
    public static $debugkey = "xwork#2016";

    // 是否记录xworklog
    public static $debug_errlog = false;

    // 是否合并xworklog
    public static $debug_mergexworklog = false;

    // 日志存放目录
    public static $debug_logpath = "/tmp/xworklog";

    public static $noticer = null;

    // 通知消息
    public static $noticeSmsMsg = "";

    // 当前纳秒值
    private static $ns = 0;

    // 工作单元唯一号,当前纳秒值,修正了后6位
    private static $unitofworkId = 0;

    // 工作单元步骤号
    private static $unitofworkStep = 0;

    public static $xunitofwork_create_close = false;

    // 合并的xworklogStr
    private static $mergeXworklogStr = "";

    // 合并的xworklogErrorStr
    private static $mergeXworklogErrorStr = "";

    // 缓存sql语句
    private static $sqls = array();

    // 统计sql执行时间
    private static $sqltimesum = 0.0;

    // 定时脚本的名称
    private static $cronName = '';

    public static function setCronName($cronName) {
        self::$cronName = $cronName;
    }

    public static function getCronName() {
        return self::$cronName;
    }

    // 设置第一个起始时间点
    public static $timeStart = null;

    public static $method_end = 0;

    public static $commit_end = 0;

    public static $page_end = 0;

    public static $noticeArr = array();

    public static function mark_timeStart() {
        self::$timeStart = XUtility::getStartTime();
    }

    public static function mark_method_end() {
        return self::$method_end = 1000 * self::getCostTimeFromStart();
    }

    public static function mark_commit_end() {
        return self::$commit_end = 1000 * self::getCostTimeFromStart();
    }

    public static function mark_page_end() {
        return self::$page_end = 1000 * self::getCostTimeFromStart();
    }

    // 添加 notice 头
    public static function addNotice($str) {
        if (strpos($str, "[--") === false) {
            $str = "[-- {$str} --]";
        }
        self::$noticeArr[] = $str;
    }

    // log4j规定了默认的几个级别: trace < debug < info < warn < error < fatal
    // xwork 日志级别: trace < sql < sys < [log] < info < warn < error
    // xwork 特殊日志: xworkdev, 用于框架的调试, 需要手工开启

    // 框架调试日志, 不要用于其他用途
    public static function xworkdev(...$params) {
        self::errlog($params, LogLevel::LEVEL_XWORK_DEV);
    }

    // 自定义日志, 用于个别问题的跟踪, 问题跟踪完毕后需要删除或改成trace
    public static function log(...$params) {
        self::trace(...$params);
    }

    // 追踪, 程序的运行过程, 工程师主用的一个级别
    // 可以通过配置文件关闭线上trace日志
    public static function trace(...$params) {
        self::errlog($params, LogLevel::LEVEL_TRACE);
    }

    // sql, 执行日志, 可以理解为特殊的trace日志
    public static function sql(...$params) {
        self::errlog($params, LogLevel::LEVEL_SQL);
    }

    // 系统, 框架层运行日志, 业务层不要用
    public static function sys(...$params) {
        self::errlog($params, LogLevel::LEVEL_SYS);
    }

    // 重要信息, 用于记录不发消息的 warn (sjp: 本级别日志的含义是修改过了的)
    // 用[INF]是为了和[SQL]对齐
    public static function info(...$params) {
        self::errlog($params, LogLevel::LEVEL_INFO);
    }

    // 会出现潜在错误的情形; (sjp: 可以理解为重点跟踪的info)
    // 同时记录到 .error.txt
    public static function warn(...$params) {
        self::errlog($params, LogLevel::LEVEL_WARN);
    }

    // 指出虽然发生错误事件，但仍然不影响系统的继续运行; (log4j::error的概念)
    // 系统致命错误; (log4j::fatal的概念)
    // 同时记录到 .error.txt
    public static function error(...$params) {
        self::errlog($params, LogLevel::LEVEL_ERROR);
    }

    // 将自定义调试信息记录到错误日志 trace < info < warn < error
    private static function errlog($params, $logLevel) {
        if (!LogLevel::couldLog($logLevel)) {
            return false;
        }

        $str = self::combineLogStr($params, $logLevel);
        if ($str === false) {
            return false;
        }

        // 标记开始时间
        if (empty(self::$timeStart)) {
            Debug::mark_timeStart();
        }

        // 需要主动初始化
        if (self::$unitofworkId < 1) {
            return;
        }

        $str = self::mergeLine($str);

        // 如果需要则显示
        self::debugshow($str);

//        $mustClose = Config::getConfig("mustClose", false);

        $unitofworkIdAndStep = self::getUnitofworkIdAndStep();
        $costTimeFromStartStr = self::getCostTimeFromStartStr();
        $str = XUtility::time_millsecond_strNoDay() . " {$unitofworkIdAndStep} [{$costTimeFromStartStr}] {$str}\n";

        // 合并日志
        if (self::$debug_mergexworklog) {
            self::$mergeXworklogStr .= $str;
            if (LogLevel::couldWrite2ErrorLog($logLevel)) {
                self::$mergeXworklogErrorStr .= $str;
            }
        } else {
            // 不合并直接写入
            self::writeXworklog2file($str);
            if (LogLevel::couldWrite2ErrorLog($logLevel)) {
                self::writeXworklog2errorfile($str);
            }
        }
    }

    // 将日志多行合并为一行
    private static function mergeLine($str) {
        $str = str_replace("\r", ' ', $str);
        $str = str_replace("\n", ' ', $str);
        $str = str_replace("\t", ' ', $str);
        $str = str_replace('    ', ' ', $str);
        $str = str_replace('    ', ' ', $str);
        $str = str_replace('   ', ' ', $str);
        $str = str_replace('  ', ' ', $str);
        $str = str_replace('  ', ' ', $str);
        return $str;
    }

    // 创建 XUnitOfWork
    public static function tryCreateXUnitOfWork(UnitOfWork $unitOfWork) {
        // 关闭本请求的 xunitofwork 和 xobjlog 记录
        if (Debug::$xunitofwork_create_close) {
            return false;
        }

        // xworkdbOpen 开启
        if (false == Config::getConfig("xworkdbOpen", false)) {
            return false;
        }

        $cnt = 0;
        $cnt += $unitOfWork->getInfoForXunitofwork('commit_insert_cnt');
        $cnt += $unitOfWork->getInfoForXunitofwork('commit_update_cnt');
        $cnt += $unitOfWork->getInfoForXunitofwork('commit_delete_cnt');
        $cnt += $unitOfWork->getInfoForXunitofwork('commit_fix_cnt');

        $host = getenv('HTTP_HOST');
        $pos = strpos($host, '.');
        $domain = substr($host, $pos + 1);
        $sub_domain = substr($host, 0, $pos);

        $xaction = strtolower(XRequest::getValue("xaction", ''));
        $method = strtolower(XRequest::getValue("method", ''));

        // cron脚本或audit, 如果没有数据生成或修改, 不生成XUnitOfWork
        if ($cnt > 0 || ($sub_domain != 'audit' && $xaction && $method)) {
            self::createXUnitOfWorkImp($unitOfWork, $domain, $sub_domain, $xaction, $method);
        }

        return true;
    }

    public static function createXUnitOfWorkImp(UnitOfWork $unitOfWork, $domain, $sub_domain, $xaction, $method) {
        $cookie = json_encode($_COOKIE, JSON_UNESCAPED_UNICODE);
        $postStr = json_encode($_POST, JSON_UNESCAPED_UNICODE);

        $theUrl = getenv('HTTP_HOST') . "" . getenv('REQUEST_URI');
        if ($theUrl) {
            $theUrl = "http://" . $theUrl;
        }
        $refererUrl = getenv('HTTP_REFERER');

        // 这个字段,暂时用于标识是否微信群消息, Config::getConfig("cacheOpen", $cacheopen);
        $cacheopen = 0;
        if (strpos($theUrl, 'from=singlemessage') > 0) {
            $cacheopen = 11;
        } elseif (strpos($theUrl, 'from=groupmessage') > 0) {
            $cacheopen = 12;
        } elseif (strpos($theUrl, 'from=timeline') > 0) {
            $cacheopen = 13;
        }

        $xunitofworkid = Debug::getUnitofworkId();
        $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

        $row = array();
        $row["id"] = $xunitofworkid;
        $row["randno"] = $randno;
        $row["server_ip"] = $_SERVER["SERVER_ADDR"];
        $row["client_ip"] = XUtility::getonlineip();
        $row["dev_user"] = XRequest::getValue('dev_user', '');
        $row["domain"] = $domain;
        $row["sub_domain"] = $sub_domain ? $sub_domain : 'cron';
        $row["action_name"] = $xaction ? $xaction : Debug::getCronName();
        $row["method_name"] = $method;
        $row["cacheopen"] = $cacheopen;
        $row["commit_load_cnt"] = $unitOfWork->getInfoForXunitofwork('commit_load_cnt');
        $row["commit_insert_cnt"] = $unitOfWork->getInfoForXunitofwork('commit_insert_cnt');
        $row["commit_update_cnt"] = $unitOfWork->getInfoForXunitofwork('commit_update_cnt');
        $row["commit_delete_cnt"] = $unitOfWork->getInfoForXunitofwork('commit_delete_cnt');
        $row["method_end"] = self::$method_end;
        $row["commit_end"] = self::$commit_end;
        $row["page_end"] = 0;
        $row["url"] = $theUrl;
        $row["referer"] = $refererUrl;
        $row["cookie"] = $cookie;
        $row["posts"] = $postStr;

        $dbconf = [];
        $dbconf['tableno'] = $randno;
        $entity = XUnitOfWork::createByBiz($row, $dbconf);

        $dbExe = BeanFinder::get("DbExecuter", 'xworkdb');
        $sqls = $entity->getInsertCommand();
        foreach ($sqls as $a) {
            $sql = $a['sql'];
            $param = $a['param'];
            $dbExe->executeNoQuery($sql, $param);
        }
    }

    // 生成请求的第0条日志, NOTICE
    private static function getNoticeStr() {
        $timeStr = XUtility::time_millsecond_strNoDay(self::$timeStart);

        if (!getenv('HTTP_HOST')) {
            $noticeStr = $timeStr . " [" . self::$unitofworkId . "][ 0] \e[32m[SCRIPT]\e[m";

            $noticeStr .= " \e[33m[" . self::$cronName . "]\e[m";
            $noticeStr .= "\n";
            return $noticeStr;
        }

        $theUrl = "http://" . getenv('HTTP_HOST') . "" . getenv('REQUEST_URI');
        $refererUrl = getenv('HTTP_REFERER');
        $_USER_AGENT = getenv("HTTP_USER_AGENT");
        $client_ip = XUtility::getonlineip();
        $noticeStr = "\e[35m[NTC]\e[m ";

        $noticeStr .= "[-- ";
        $noticeStr .= "[server_ip = {$_SERVER["SERVER_ADDR"]}] ";
        $noticeStr .= "[client_ip = {$client_ip}] ";
        $noticeStr .= "[theUrl = {$theUrl}] ";
        $noticeStr .= "[referer = {$refererUrl}] ";
        $noticeStr .= "[_USER_AGENT = {$_USER_AGENT}] ";

        $postStr = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        if (false == empty($_POST) && false == empty($postStr)) {
            $noticeStr .= "[posts = {$postStr}] ";
        } else {
            $noticeStr .= '[posts = ] ';
        }

        $inputstr = file_get_contents('php://input');
        if(false == empty($inputstr)){
            $noticeStr .= "[php://input = {$inputstr}] ";
        }

        $cookieStr = json_encode($_COOKIE, JSON_UNESCAPED_UNICODE);

        if (false == empty($_COOKIE) && false == empty($cookieStr)) {
            $noticeStr .= "[cookies = {$cookieStr}] ";
        }

        $noticeStr .= " --] ";

        if (self::$noticeArr) {
            $noticeStr .= implode(' ', self::$noticeArr);
        }

        $noticeStr = self::mergeLine($noticeStr);

        $noticeStr = $timeStr . " [" . self::$unitofworkId . "][ 0] [00.000 ms] {$noticeStr}\n";

        return $noticeStr;
    }

    public static function canShowDebugInfo() {
        if (self::$debug == 'Dev') {
            return true;
        }

        if (isset($_GET["debugkey"]) && self::$debugkey == $_GET["debugkey"]) {
            return true;
        }

        if (isset($_COOKIE["debugkey"]) && self::$debugkey == $_COOKIE["debugkey"]) {
            return true;
        }

        return false;
    }

    // 记录总耗时, 截至当前时刻,全部耗时信息字符串
    public static function logCostTimeStr($pos = 'where: ', $isSYS = true) {
        $timeSpan = XUtility::getCostTime(self::$timeStart);
        $timeSpan = XUtility::trimTimeSpan($timeSpan);
        $sqltimesum = Debug::getSqltimesum();
        if ($isSYS) {
            Debug::sys("[-- {$pos} {$timeSpan} ms | {$sqltimesum} ms --]");
        } else {
            Debug::trace("[-- {$pos} {$timeSpan} ms | {$sqltimesum} ms --]");
        }
    }

    // 获取时间差, 字符串
    public static function getCostTimeFromStartStr() {
        if (empty(self::$timeStart)) {
            return "";
        } else {
            $timeSpan = XUtility::getCostTime(self::$timeStart);
            $timeSpan = XUtility::trimTimeSpan($timeSpan);
            return "{$timeSpan} ms";
        }
    }

    // 从启动时刻到现在耗时
    public static function getCostTimeFromStart() {
        return XUtility::getCostTime(self::$timeStart);
    }

    // 写硬盘,如果没有主动调用,最好由ob_start 的callback 来调用
    public static function flushXworklog() {
        // 如果不是必须记,并且没有异常时则不记log
        $mustXworklog = Config::getConfig("mustXworklog", true);
        if (!$mustXworklog && empty(self::$mergeXworklogErrorStr)) {
            return;
        }

        // 记录总耗时
        Debug::logCostTimeStr('PageEnd: ');

        // 补一条 xwork-end
        Debug::sys("[---------- xwork-end ----------]");

        // notice 加入总耗时
        Debug::addNotice(self::getCostTimeFromStartStr());

        // NoticeStr
        $noticeStr = self::getNoticeStr();

        // str
        $str = "\n\n" . $noticeStr;

        // mergeXworklogStr
        $str .= self::$mergeXworklogStr;

        self::writeXworklog2file($str); // 写日志文件
        self::$mergeXworklogStr = ""; // 重置

        // mergeXworklogErrorStr => errorStr
        $errorStr = self::$mergeXworklogErrorStr;
        // $errorStr = trim($errorStr);

        self::writeXworklog2errorfile($errorStr); // 写error日志文件
        self::$mergeXworklogErrorStr = ""; // 重置

        // 通知
        if ($errorStr && self::$noticer) {
            self::$noticer->send(self::$unitofworkId, $errorStr, $str);
        }

        Debug::mark_page_end();

        // xworkdbOpen 开启, 且未关闭本请求日志
        if (Config::getConfig("xworkdbOpen", false) && false == Debug::$xunitofwork_create_close) {

            $xunitofworkid = self::getUnitofworkId();
            $page_end = self::$page_end;

            $tableno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

            $sql = "update xunitofworks{$tableno}
                set page_end = '{$page_end}'
                where id={$xunitofworkid}";

            $dbExe = BeanFinder::get("DbExecuter", 'xworkdb');
            $dbExe->executeNoQuery($sql);
        }

        self::$unitofworkId = 0;
        self::$unitofworkStep = 0;
    }

    //错误日志写文件
    private static function writeXworklog2errorfile($str) {
        $filename = date("Ymd", time()) . ".error.txt";
        self::writeToFile($str, $filename);
    }

    //全部日志写文件
    private static function writeXworklog2file($str) {
        $filename = date("Ymd", time()) . ".txt";
        self::writeToFile($str, $filename);
    }

    //真正的写磁盘
    private static function writeToFile($str, $filename) {
        if (empty($str)) {
            return;
        }

        if (empty($filename)) {
            return false;
        }

        $logpath = self::$debug_logpath;
        if (!is_dir($logpath)) {
            mkdir($logpath, 0777);
        }

        $filename = $logpath . "/" . $filename;
        if (!file_exists($filename)) {
            touch($filename);
            chmod($filename, 0666);
        }

        error_log($str, 3, $filename);
    }


    // unitofworkId ++
    public static function plusplusUnitofworkId() {
        self::getUnitofworkIdImp();

        // 用id生成器取一个id
        $nextId = EntityBase::createID();

        // 修正末6位
        self::$unitofworkId = self::$ns + $nextId % 1000000;
    }

    // 初始化 UnitofworkId
    public static function initUnitofworkId() {
        self::getUnitofworkIdImp();
    }

    // 监控用途
    public static function getUnitofworkId() {
        return self::getUnitofworkIdImp();
    }

    // 初始化
    private static function getUnitofworkIdImp() {

        // 未初始化
        if (self::$unitofworkId < 1) {

            $X_REQUEST_ID = $_SERVER['X_REQUEST_ID'];

            // 10位数字, 秒, 1234567890 => 2009-02-14 07:31:30
            if ($X_REQUEST_ID > 1234567890123456789) {
                // 19位数字, 纳秒级
                $ms = $X_REQUEST_ID / 1000000;
            } elseif ($X_REQUEST_ID > 1234567890123456) {
                // 16位数字, 微妙级
                $ms = $X_REQUEST_ID / 1000;
            } elseif ($X_REQUEST_ID > 1234567890123) {
                // 13位数字, 毫秒
                $ms = $X_REQUEST_ID;
            } else {
                // 获取毫秒 = 秒*1000
                $ms = microtime(true) * 1000;
            }

            // 取整
            $ms = sprintf("%d", $ms);

            // 微妙级 => 纳秒级
            $ns = $ms * 1000000;

            // 缓存
            self::$ns = $ns;

            // 用id生成器取一个id
            $nextId = EntityBase::createID();

            // 修正末6位
            $unitofworkId = self::$ns + $nextId % 1000000;

            self::$unitofworkId = $unitofworkId;
        }

        return self::$unitofworkId;
    }

    // 获取工作单元唯一号+步骤号
    private static function getUnitofworkIdAndStep() {
        self::getUnitofworkIdImp();

        self::$unitofworkStep++;

        return sprintf("[%s][%2d]", self::$unitofworkId, self::$unitofworkStep);
    }

    // 记录sql语句
    public static function addSql($sql, $timespan, $isquery = true) {
        self::$sqltimesum += $timespan;

        $sqlItem = array();
        $sqlItem["sql"] = $sql;
        $sqlItem["timespan"] = $timespan;
        // $sqlItem["isquery"] = $isquery;
        self::$sqls[] = $sqlItem;
    }

    // 获得sql执行时间，并取整
    public static function getSqltimesum() {
        return XUtility::trimTimeSpan(self::$sqltimesum);
    }

    // 调试环境中打印sql缓存
    public static function print_sqls() {
        if (self::canShowDebugInfo()) {
            print_r(self::$sqls);
        }
    }

    // 在调试环境中打印对象信息
    public static function var_dump($obj) {
        if (self::canShowDebugInfo()) {
            echo "<pre>";
            var_dump($obj);
            echo "</pre>";
        }
    }

    // 输出调试信息与当前时间
    public static function echoline($line, $echotime = false) {
        if ($echotime) {
            echo XUtility::time_microtime_str() . " {$line} <br>\n";
        } else {
            echo "{$line} <br>\n";
        }
    }

    // 在调试环境中打印出错信息
    private static function debugshow($message, $must = false) {
        if ($must || self::canShowDebugInfo()) {
            echo "<pre>";
            var_dump($message);
            echo "</pre>";
        }
    }

    // /////////////////////////////////////////////

    // 将自定义异常信息记录到错误日志
    public static function errlogEx($ex, $exName = "Ex", $must = true, $logMessage = '') {
        $mustClose = Config::getConfig("mustClose", false);

        if (!$mustClose) {
            $SERVER_ADDR = @$_SERVER["SERVER_ADDR"];
            self::$noticeSmsMsg = "[{$SERVER_ADDR}][" . self::$unitofworkId . "][{$exName}]" . $ex->getMessage();
        }

        if ($logMessage) {
            $msg = $logMessage;
        } else {
            $msg = $ex->getMessage();
        }
        $str = "[-- {$exName}:= {$msg} --]";
        Debug::error($str);

        $str = "[-- {$exName}Trace:= ";
        $str .= $ex->getTraceAsString();
        $str .= " --]";
        Debug::error($str);
    }

    // 向前兼容的旧代码
    public static function errlogStackPoint($message = "断点") {
        self::exStackPoint($message);
    }

    // 将异常信息到错误日志，并打印错误位置
    public static function exStackPoint($message = "断点") {
        try {
            throw new Exception($message);
        } catch (Exception $ex) {
            Debug::sys($message);
            Debug::sys($ex->getTraceAsString(), false);
        }
    }

    // 获得当前时间
    public static function getCurrentTime() {
        return microtime(true);
    }

    // 获得消耗时间差
    public static function getCostTime($timeStart) {
        return (microtime(true) - $timeStart) * 1000;
    }

    private static function combineLogStr($params, $logLevel) {
        if (count($params) < 1) {
            return false;
        }
        foreach ($params as &$param) {
            if ($param instanceof Entity) {
                $param = "(Entity:" . $param->getClassName() . ")" . json_encode($param->toArray(), JSON_UNESCAPED_UNICODE);
            } else if ($param instanceof stdClass || is_array($param)) {
                $param = json_encode($param, JSON_UNESCAPED_UNICODE);
            } else if (is_object($param)) {
                //类实例需要实现JsonSerializable接口才能encode
                $className = get_class($param);
                $tmp = "($className)";
                if ($param instanceof JsonSerializable) {
                    $tmp .= json_encode($param, JSON_UNESCAPED_UNICODE);
                }
                $param = $tmp;
            } else if (is_bool($param)) {
                $param = $param === true ? '(bool)true' : '(bool)false';
            }
        }
        $str = implode(' ', $params);
        $str = LogLevel::getColorLevelStr($logLevel) . $str;
        return $str;
    }
}
