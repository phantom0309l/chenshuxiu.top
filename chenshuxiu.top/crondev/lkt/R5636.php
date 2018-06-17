<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/1/31
 * Time: 11:21
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

// 给有失访记录的患者，标记失访

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

$unitofwork = BeanFinder::get("UnitOfWork");

$sql = "SELECT a.*
        FROM patients a
        LEFT JOIN patientrecords b ON a.id = b.patientid
        WHERE b.code = 'common'
        AND b.type = 'lose'
        AND a.is_lose = 0
        AND a.id NOT IN (
            SELECT a.patientid
            FROM patientrecords a
            LEFT JOIN wxtxtmsgs b ON a.patientid = b.patientid
            WHERE a.code = 'common'
            AND a.type = 'lose'
            AND b.createtime > a.createtime
            GROUP BY a.patientid
        )
        GROUP BY a.id";
$patients = Dao::loadEntityList("Patient", $sql);

$brief = 0;
foreach ($patients as $patient) {
    echo $patient->id . "\n";
    $patient->lose();

    $brief++;

    if ($brief % 100 == 0) {
        $unitofwork->commitAndInit();
    }
}
$unitofwork->commitAndInit();

$diseaseids = Disease::getCancerDiseaseidsStr();
$sql = "SELECT *
        FROM patients 
        WHERE id NOT IN (
            SELECT a.id
            FROM patients a
            INNER JOIN optasks b ON a.id = b.patientid
            WHERE b.status IN (0, 2)
        )
        AND is_live = 1
        AND diseaseid IN ({$diseaseids})";

$patients = Dao::loadEntityList("Patient", $sql);

$brief = 0;
foreach ($patients as $patient) {
    echo $patient->id . "\n";
    $patient->lose();

    $brief++;

    if ($brief % 100 == 0) {
        $unitofwork->commitAndInit();
    }
}


echo "$brief\n\n";
$unitofwork->commitAndInit();