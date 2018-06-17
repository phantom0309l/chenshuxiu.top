<?php

class TheSystem
{

    // 系统初始化函数
    public static function init ($entryFile = '', $ob_start_callback_flushxworklog = false) {
        self::init_ini_set();

        Config::setConfigFile(dirname(__FILE__) . "/../sys/config.php");

        Debug::initUnitofworkId();

        self::initErrorHandler();

        self::initDebugFlag();

        $arr = explode("/", $entryFile);
        $fileName = array_pop($arr);
        if ($fileName == 'index.php') {
            $fileName = '';
        }
        Debug::setCronName($fileName);

        // 合并xworklog
        if (Debug::$debug_mergexworklog && $ob_start_callback_flushxworklog) {
            ob_start("callback_flushxworklog");
        } else {}

        // dtpl
        XContext::setValue("dtpl", ROOT_TOP_PATH . "/domain/tpl");
    }

    // 初始化php变量
    public static function init_ini_set () {
        ini_set("arg_seperator.output", "&amp;");
        ini_set("magic_quotes_gpc", 0);
        ini_set("magic_quotes_sybase", 0);
        ini_set("magic_quotes_runtime", 0);
        mb_internal_encoding("UTF-8");
    }

    // 初始化debug参数
    public static function initDebugFlag () {
        Debug::$debug = Config::getConfig("debug", "Dev1");
        Debug::$debugkey = Config::getConfig("debugkey", "fcqx");
        Debug::$debug_errlog = Config::getConfig("debug_errlog", FALSE);
        Debug::$debug_mergexworklog = Config::getConfig("debug_mergexworklog", TRUE); // 默认合并xworklog日志
        Debug::$debug_logpath = Config::getConfig("debug_logpath");
        Debug::$noticer = new WxNoticer();
    }

    // 设置系统出错处理钩子
    public static function initErrorHandler () {
        register_shutdown_function(errorShutdown);
        set_error_handler(xErrorHandler, E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT);
    }
}

// 写日志文件
function callback_flushxworklog ($buffer) {
    Debug::sys("[-- callback_flushxworklog --]");

    $the_domain = XContext::getValue('the_domain');
    // Debug::sys("----[ {$the_domain} ]----");
    // Debug::sys("[-- end --]");

    // 页面请求
    if (strpos($the_domain, XContext::getValue('website_domain')) > 1) {
        Debug::flushXworklog();
    }

    return $buffer;
}

$the_system_log = array();

function xErrorHandler ($error, $message, $file, $line) {
    global $the_system_log;
    switch ($error) {
        case E_ERROR:
            $type = 'FATAL_ERROR';
            break;
        case E_WARNING:
            $type = 'WARNING';
            break;
        case E_NOTICE:
            $type = 'NOTICE';
            break;
        default:
            $type = 'Unknown error type [' . $error . ']';
            break;
    }
    $log = $type . ': ' . $message . ' in line ' . $line . ' of file ' . $file . ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ')';
    if (function_exists('debug_backtrace')) {
        $backtrace = debug_backtrace();
        for ($level = 1; $level < count($backtrace); $level ++) {
            $message = 'File: ' . $backtrace[$level]['file'] . ' Line: ' . $backtrace[$level]['line'] . ' Function: ';
            if (IsSet($backtrace[$level]['class'])) {
                $message .= '(class ' . $backtrace[$level]['class'] . ') ';
            }
            if (IsSet($backtrace[$level]['type'])) {
                $message .= $backtrace[$level]['type'] . ' ';
            }
            $message .= $backtrace[$level]['function'] . '(';
            if (IsSet($backtrace[$level]['args'])) {
                for ($argument = 0; $argument < count($backtrace[$level]['args']); $argument ++) {
                    if ($argument > 0) {
                        $message .= ', ';
                    }
                    $message .= json_encode($backtrace[$level]['args'][$argument], JSON_UNESCAPED_UNICODE);
                }
            }
            $message .= ')';
            $log .= " #" . ($level - 1) . ' ' . $message . ' ';
        }
    }
    $the_system_log[$type][] = $log;
}

function errorShutdown () {
    global $the_system_log;
    if ($e = error_get_last()) {
        if ($e['type'] == E_ERROR || $e['type'] == E_WARNING) {
            static $errorDesc = array(
                E_ERROR => 'FATAL_ERROR',
                E_WARNING => 'WARNING');
            $type = $errorDesc[$e['type']];
            $the_system_log[$type][] = $type . ': ' . $e['message'] . " in " . $e['file'] . ' line ' . $e['line'];
        }
    }
    if (! $the_system_log || ! is_array($the_system_log)) {
        return;
    }
    ksort($the_system_log); // 始终让ERROR 信息先记录，为了报警
    foreach ($the_system_log as $type => $one) {
        if ($type == 'FATAL_ERROR') {
            foreach ($one as $a) {
                Debug::error($a);
            }

            // 如果是定时脚本,通知并记 xworklog 日志
            if (Debug::getCronName()) {
                Debug::flushXworklog();
            }
        } else {
            foreach ($one as $a) {
                Debug::warn($a);
            }
        }
    }
}
