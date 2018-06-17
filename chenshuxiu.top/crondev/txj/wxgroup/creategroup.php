<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now();

$unitofwork = BeanFinder::get("UnitOfWork");

$wxshop = WxShop::getById(8);
$result = WxApi::createGroup( $wxshop, "mall");
echo "\n-----pgroupid[{$result}]----- ";

$unitofwork->commitAndInit();

Debug::trace("=====[cron][end][group.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
