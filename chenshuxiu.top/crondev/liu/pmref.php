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

$unitofwork = BeanFinder::get("UnitOfWork");

$patient = Patient::getById(101886789);
$medicine = Medicine::getById(4);

$patient->stopDrug($medicine, date("Y-m-d"), "lala");

$unitofwork->commitAndInit();
