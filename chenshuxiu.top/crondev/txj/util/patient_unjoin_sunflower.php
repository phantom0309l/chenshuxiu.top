<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Patient_unjoin_sunflower
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $patient = Patient::getById(645884976);

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patient->id);
        $patient_hezuo->remove();

        $optask = OpTask::getById(645885004);
        $optask->status = 0;

        $createwxuser = $patient->createuser->createwxuser;

        //134 礼来
        //141 开药门诊
        //142 非开药门诊
        WxApi::MvWxuserToGroup($createwxuser, 141);

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Patient_unjoin_sunflower.php]=====");

$process = new Patient_unjoin_sunflower();
$process->dowork();

Debug::trace("=====[cron][end][Patient_unjoin_sunflower.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
