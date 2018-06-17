<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/11/29
 * Time: 16:41
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
$unitofwork = BeanFinder::get("UnitOfWork");

$sql = "UPDATE optasks a
        LEFT JOIN patients b ON a.patientid = b.id
        SET a.status = 1
        WHERE b.diseaseid IN (2, 3, 6, 22)
        AND optasktplid = 123269925;";
echo Dao::executeNoQuery($sql) . "\n\n";

echo "\n\n-----end----- " . XDateTime::now() . "\n\n";
$unitofwork->commitAndInit();