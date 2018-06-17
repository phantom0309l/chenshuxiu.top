<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/9/21
 * Time: 16:52
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

$sql = "SELECT *
        FROM optasks
        WHERE optasktplid = 123269225 
        AND plantime >= '2017-08-21' 
        AND status IN (0, 2) 
        AND diseaseid = 3
        GROUP BY patientid";

$optasks = Dao::loadEntityList("OpTask", $sql);

$diseaseid = 3;
foreach ($optasks as $optask) {
    $patient = $optask->patient;

    $padrMonitors = PADRMonitorDao::getListByPatientid($patient->id);
    if (!empty($padrMonitors)) {
        continue;
    }

//    PADRMonitorService::updateMonitorByPatientForDrug($patient, $diseaseid);
    OpTaskStatusService::changeStatus($optask, 1);
}

echo "\n\n";
BeanFinder::get("UnitOfWork")->commitAndInit();