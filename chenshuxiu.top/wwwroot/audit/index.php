<?php
// please enable the line below if you are having memory problems
// ini_set('memory_limit', "16M");
// just to make php use &amp; as the separator when adding the PHPSESSID
// variable to our requests // aa
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
// 不允许打开！！学会看php错误日志+nginx错误日志
// ini_set('display_errors', 1);

// phpinfo();
// error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

if ($_GET['debug'] == 'fcqx' || $_GET['debugkey'] == 'fcqx') {
    error_reporting(E_ALL ^ E_NOTICE);
    // ini_set('display_errors', 1);
}
// load Config and Assembly
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/audit/Assembly.php");
mb_internal_encoding("UTF-8");

if (extension_loaded('xhprof') && XRequest::getValue('xhprof', false) == 1) {
    xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
}

TheSystem::init(__FILE__, true);

Config::setConfig('innerSystem', true);

$memory_start = memory_get_usage();
XContext::setValue("memory_start", $memory_start);

Config::setConfig("cacheNeedReload", true);
if (isset($_GET["nocache"]) || isset($_GET["cacheNeedReload"])) {
    Config::setConfig("cacheNeedReload", true);
}

Config::setConfig("needUrlRewrite", true);
$tplPath = ROOT_TOP_PATH . "/audit/tpl/";

$mapFile = ROOT_TOP_PATH . "/audit/ActionMap.properties.php";
$controller = new XController($mapFile, $tplPath);
$controller->process();

if (extension_loaded('xhprof') && XRequest::getValue('xhprof', false) == 1) {
    $xhprof_data = xhprof_disable();

    $XHPROF_ROOT = "/home/www/tool/xhprof"; // 这里填写的就是你的xhprof的路径
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new XHProfRuns_Default();

    $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
    echo '<a target="_blank" href="http://tool.fangcunhulian.cn/xhprof/xhprof_html/callgraph.php?run=' . $run_id . '&sort=fn&source=xhprof_foo&all=1">性能分析</a>';
    exit(0);
}

if (! empty($echoTime))
    echo "<br>" . XContext::getValue("AllCostTime");
