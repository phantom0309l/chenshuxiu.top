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

// Debug::$debug = 'Dev';

class Init_patient_istest
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from patients";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                $i ++;
                echo "\n====[$i][{$id}]===\n";
                if($patient->isTest()){
                    $patient->is_test = 1;
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
Debug::trace("=====[cron][beg][Init_patient_istest.php]=====");

$process = new Init_patient_istest();
$process->dowork();

Debug::trace("=====[cron][end][Init_patient_istest.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
