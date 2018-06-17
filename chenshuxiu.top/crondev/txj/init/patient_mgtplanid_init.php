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

class Patient_mgtplanid_init
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select a.id from patients a
                    left join patient_hezuos b on b.patientid = a.id
                    where a.diseaseid=1 and a.doctorid in (1,2,3,537) and (b.status != 1 or b.id is null) and a.mgtplanid = 0";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $j = 0;

        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                $i ++;
                $j ++;
                echo "\n====[$j][{$id}]===\n";

                $optasktpl_firstTel = OpTaskTplDao::getOneByUnicode('firstTel:audit');
                $optask = OpTaskDao::getOneByPatient($patient, " and optasktplid = {$optasktpl_firstTel->id} and level = 5 ");
                if($optask instanceof OpTask){
                    $is_closed = $optask->isClosed();
                    if(false == $is_closed){
                        continue;
                    }
                }

                //六院管理计划
                $mgtPlan = MgtPlanDao::getByEname("pkuh6");
                if($mgtPlan instanceof MgtPlan){
                    $patient->mgtplanid = $mgtPlan->id;
                }
            }
            if($i>150){
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Patient_mgtplanid_init.php]=====");

$process = new Patient_mgtplanid_init();
$process->dowork();

Debug::trace("=====[cron][end][Patient_mgtplanid_init.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
