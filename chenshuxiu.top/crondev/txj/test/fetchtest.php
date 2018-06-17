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

XContext::setValue("dtpl", ROOT_TOP_PATH . "/domain/tpl");

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQG/8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL1RqajVONG5sMXNPdnhMR1lOQkJNAAIEpWHNVgMEAAAAAA==";

$picture = Picture::createByFetch($url);
echo $picture . "====aaaa\n";
print_r($picture);
echo $picture->id . "====aaaa\n";

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
