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

$ids = array(
    100907587,
    100907585,
    100907127,
    100907125,
    100907119,
    100907117,
    100907115,
    100907113,
    100900909,
    100897767);

$unitofwork = BeanFinder::get("UnitOfWork");

foreach ($ids as $id) {
    $sheet = XQuestionSheet::getById($id);
    $title = $sheet->title;
    $row = array();
    $row['xquestionsheetid'] = $id;
    $row["title"] = $title;
    $row["groupstr"] = "wenzhen";
    $row["ename"] = "";
    $row["brief"] = $title;
    $row["content"] = $title;

    $papertpl = PaperTpl::createByBiz($row);

    $sheet->set4lock("objtype", "PaperTpl");
    $sheet->set4lock("objid", $papertpl->id);

}

$unitofwork->commitAndInit();

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
